<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use App\ProjectUser;
use App\Company;
use App\Goal;
use App\Statement;
use App\Timeline;
use App\GoalUser;
use App\Setting;
use App\CreditPoint;
use App\Leave;
use App\NewsFeed;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Helpers\DateString;
use App\Http\Resources\ApiUserResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\GoalResource;
use App\Http\Resources\TimelineResource;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\NewsFeedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ApiUserController extends Controller
{
    public function employee(Request $request)
    {
        $input = $request->all();

        $user = Auth::user();

        $start = $request->start_date;
        $end = $request->end_date;

        $credit_points = CreditPoint::where(function ($query) use ($start, $end, $user, $request) {

            if (!$start && !$end) {
                $start = Carbon::now()->startOfMonth()->subMonth()->toDateTimeString();
                $end = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();
            }

            $query->whereBetween('created_at', array($start, $end))->where('user_id', $user->id);
        })->get();

        $points = $credit_points->sum('points');

        return ApiUserResource::make([
            'user' => $user,
            'credit_points' => $credit_points,
            'points' => $points
        ])->additional(['meta' => [
            'Credit Points' => ($request->start_date) ? $this->getString($start, $end) : "Credit Points"
        ]]);
    }

    public function company(Request $request)
    {
        $company = Company::find(Auth::user()->company_id);

        return CompanyResource::make($company);
    }

    public function projects(Request $request)
    {
        $input = $request->all();

        $qb = Auth::user()->Projects()->orderBy('created_at', 'DESC')->with(['Company', 'Users']);

        $qb = QB::whereLike($input, "name", $qb);
        $qb = QB::whereBetween($input, "start_date", $qb);
        $qb = QB::whereBetween($input, "end_date", $qb);
        $qb = QB::where($input, "status", $qb);
        $qb = QB::where($input, "company_id", $qb);
        $qb = QB::whereLike($input, "client_comment", $qb);
        $qb = QB::whereLike($input, "ceo_comment", $qb);

        $projects = $qb->paginate();

        return ProjectResource::collection($projects);
    }
    public function goals(Request $request)
    {
        $input = $request->all();

        $start = $request->start_date;
        $end = $request->end_date;

        $qb = Auth::user()->Goals()->orderBy('created_at', 'DESC')->with('Project');

        if ($start || $end) {

            $qb->where(function ($query) use ($start, $end) {

                $query->WhereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere([
                        ['start_date', '<=', $start],
                        ['end_date', '>=', $end]
                    ]);
            });
        }
        $qb = QB::where($input, "project_id", $qb);
        $qb = QB::whereLike($input, "ceo_comment", $qb);
        $qb = QB::where($input, "status", $qb);

        $goals = $qb->paginate();

        return GoalResource::collection($goals)->additional(['meta' => [
            'Goals_String' => ($start) ? DateString::getDateString($start, $end, "Goals") : "Goals"
        ]]);
    }

    public function editGoal(Request $request, $id)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'status' => 'required',
            'user_remarks' => 'present',
        ]);

        $goal = Auth::user()->Goals()->find($id);

        if (!$goal) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Goal')
            ]));
        }

        $goal_status = $goal->status;

        $status = $data['status'];

        if (!$status == $goal_status) {

            if ($status != Goal::$COMPLETED) {

                abort(400, __('messages.model.complete.status', [
                    'model' => __('messages.Goal')
                ]));
            }
            if ($goal_status != Goal::$ONGOING) {

                abort(400, __('messages.model.ongoing.status', [
                    'model' => __('messages.Goal')
                ]));
            }

            if ($goal_status == Goal::$ONGOING) {

                $goal->update([
                    'status' => $status,
                    'isApproved' => 0,
                ]);
            }
        }
        $remarks = GoalUser::where([['user_id', Auth::user()->id], ['goal_id', $id]]);

        $remarks->update([
            'user_remarks' => $data['user_remarks'],
        ]);
        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.User Remarks')
            ]),
            'data' => new GoalResource($goal)
        ];

    }

    public function timelines(Request $request)
    {
        $input = $request->all();
        $user_id = Auth::user()->id;

        $totalhours = Setting::getValue('total_working_hours');

        $totalhours = Carbon::createFromFormat('H', $totalhours)->toTimeString();

        $start = Carbon::now()->startOfMonth()->toDateTimeString();
        $end = Carbon::now()->endOfMonth()->toDateTimeString();

        if ($request->start_date || $request->end_dates) {

            $start = Carbon::parse($request->start_date)->format('Y/m/d');
            $end = Carbon::parse($request->end_date)->format('Y/m/d');
        }

        $time = DB::select(DB::raw("SELECT 
        timelines.date,
        timelines.login_time,
        timelines.logout_time,
        timelines.user_id,

        SEC_TO_TIME( SUM(TIME_TO_SEC( SUBTIME(logout_time,login_time)) ) ) As hours_worked,
        SUBTIME( '$totalhours' ,  SEC_TO_TIME( SUM(TIME_TO_SEC( SUBTIME(logout_time,login_time)) ) ) ) As hours_absent 
        
        FROM `timelines` 
        WHERE (user_id =  '$user_id') AND (timelines.date BETWEEN '$start' AND '$end')
        GROUP BY timelines.date
        ORDER BY (created_at) DESC
        "));

        return [
            'data' => $time,
            'meta' => [
                'Time_Line_String' => ($start) ? DateString::getDateString($start, $end, "Time Line") : "Time Line",
                'TimeLine_History' => $this->loginHistory($request),
                'Leave_History' => $this->leaveHistory($request)
            ]
        ];
    }

    public function leaves(Request $request)
    {
        $input = $request->all();

        $start_year = Carbon::now()->startOfYear()->toDateTimeString();
        $end_year = Carbon::now()->endOfYear()->toDateTimeString();

        if ($request->start_date) {
            $start_year = Carbon::parse($request->start_date)->toDateTimeString();
            $end_year = Carbon::parse($request->end_date)->toDateTimeString();
        }

        $qb = Auth::user()->Leaves()->orderBy('created_at', 'DESC')->whereBetween('leave_date', array($start_year, $end_year));

        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "detail", $qb);
        $qb = QB::where($input, "isTracked", $qb);
        $qb = QB::where($input, "isFull", $qb);
        $qb = QB::whereLike($input, "leave_type", $qb);

        $leave = $qb->paginate();

        return LeaveResource::collection($leave);
    }

    public function news(Request $request)
    {
        $input = $request->all();

        $qb = NewsFeed::orderBy('updated_at', 'DESC');
        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "news", $qb);

        $NewsFeed = $qb->paginate(5);

        return NewsFeedResource::collection($NewsFeed);
    }

    public function projectremarks(Request $request, $id)
    {
        $project = ProjectUser::where([['project_id', $id], ['user_id', Auth::user()->id]])->first();

        if (!$project) {
            abort(400, __('messages.model.not.found', [
                'model' => __('messages.User')
            ]));
        }

        $data = $request->all();

        $this->validateOrAbort($data, [
            'user_remarks' => 'required',
        ]);

        $project->update([
            'user_remarks' => $data['user_remarks'],
        ]);
        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.User Remarks')
            ]),
            'data' => ProjectResource::make($project)
        ];
    }

    public function loginHistory(Request $request)
    {
        $user_id = Auth::user()->id;

        $input = $request->all();
        $totalhours = Setting::getValue('total_working_hours');

        $totalhours = Carbon::createFromFormat('H', $totalhours)->toTimeString();

        $start_year = Carbon::now()->startOfYear()->toDateTimeString();
        $end_year = Carbon::now()->endOfYear()->toDateTimeString();
        $year = "";
        if ($request->start_date) {
            $start_year = Carbon::parse($request->start_date)->toDateTimeString();
            $end_year = Carbon::parse($request->end_date)->toDateTimeString();
            $year = ", YEAR(date)";
        }

        $timeline = DB::select(DB::raw("SELECT CONCAT(MONTHName(date),  ' '$year) as month,

        SEC_TO_TIME( SUM( TIME_TO_SEC( SUBTIME(logout_time,login_time) ))) AS total_hours_worked , 

       ADDTIME( '$totalhours','180:00:00') AS Total_hours,
        
       SUBTIME(ADDTIME( '$totalhours','180:00:00'),SEC_TO_TIME( SUM( TIME_TO_SEC( SUBTIME(logout_time,login_time) )))) AS total_hours_absents

        FROM timelines

        where (user_id= '$user_id') AND (date BETWEEN '$start_year' AND '$end_year')

        GROUP by month ORDER BY `date` DESC"));


        return $timeline;
    }

    private function leaveHistory(Request $request)
    {
        $input = $request->all();

        $user_id = Auth::user()->id;

        $start_year = Carbon::now()->startOfYear()->toDateTimeString();
        $end_year = Carbon::now()->endOfYear()->toDateTimeString();

        $total_leaves = Setting::getValue('leaves_allowed');

        $helf_leaves = Leave::where('isFull', 0)->whereBetween('leave_date', array($start_year, $end_year))->where('user_id', $user_id)->count();

        $full_leaves = Leave::where('isFull', 1)->whereBetween('leave_date', array($start_year, $end_year))->where('user_id', $user_id)->count();

        $utilized_leaves = ($helf_leaves / 2 + $full_leaves);

        $remaining_leaves = ($total_leaves - $utilized_leaves);

        return [
            'total' => $total_leaves,
            'short' => $helf_leaves,
            'full' => $full_leaves,
            'utilized' => $utilized_leaves,
            'remaining' => $remaining_leaves
        ];
    }

}