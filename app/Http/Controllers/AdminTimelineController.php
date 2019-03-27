<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimelineResource as TimelineResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Project;
use App\Timeline;
use App\User;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Leave;
use App\Helpers\DateString;

class AdminTimelineController extends Controller
{
    protected $permissions = [
        'index' => 'timeline-list',
        'update' => 'timeline-update',
        'delete' => 'timeline-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

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
        WHERE (user_id =  '$request->user_id') AND (timelines.date BETWEEN '$start' AND '$end')
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
    public function leaveHistory(Request $request)
    {
        $input = $request->all();

        $start_year = Carbon::now()->startOfYear()->toDateTimeString();
        $end_year = Carbon::now()->endOfYear()->toDateTimeString();

        $total_leaves = Setting::getValue('leaves_allowed');

        $helf_leaves = Leave::where('isFull', 0)->whereBetween('leave_date', array($start_year, $end_year))->where('user_id', $request->user_id)->count();

        $full_leaves = Leave::where('isFull', 1)->whereBetween('leave_date', array($start_year, $end_year))->where('user_id', $request->user_id)->count();

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

    public function loginHistory(Request $request)
    {
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

        where (user_id= '$request->user_id') AND (date BETWEEN '$start_year' AND '$end_year')

        GROUP by month ORDER BY `date` DESC"));


        return $timeline;
    }

    public function update(Request $request, $id)
    {
        $timeline = Timeline::find($id);
        if (!$timeline) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Timeline')
            ]));
        }

        $data = $request->all();

        $this->validateOrAbort($data, [
            'date' => 'required',
            'login_time' => 'required',
            'logout_time' => 'required'
        ]);

        $totalhours = Setting::getValue('total_working_hours');

        $login_time = $data['login_time'];

        $logout_time = $data['logout_time'];

        $totalworkinghours = (new Carbon($logout_time))->diff(new Carbon($login_time))->format('%h:%I:%S');

        $totalabsenthours = (new Carbon("$totalhours:00:00"))->diff(new Carbon($totalworkinghours))->format('%h:%I:%S');

        $timeline->update([
            'date' => $data['date'],
            'login_time' => $login_time,
            'logout_time' => $logout_time,
            'hours_worked' => $totalworkinghours,
            'hours_absent' => $totalabsenthours,
        ]);
        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Timeline')
            ]),
            'data' => TimelineResource::make($timeline)
        ];
    }
    public function delete(Timeline $timeline, $id)
    {
        $timeline = Timeline::find($id);
        if (!$timeline) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Timeline')
            ]));
        }
        $timeline->delete();
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Timeline')
            ])
        ];
    }
}

