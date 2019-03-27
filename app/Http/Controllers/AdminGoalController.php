<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Goal;
use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\GoalUser;
use Carbon\Carbon;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Http\Resources\GoalResource as GoalResources;
use App\CreditPoint;
use App\Setting;
use App\Helpers\DateString;

class AdminGoalController extends Controller
{
    protected $permissions = [
        'index' => 'goal-list',
        'create' => 'goal-create',
        'update' => 'goal-update',
        'delete' => 'goal-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

        $qb = Goal::orderBy('created_at', 'DESC')->with(['Users', 'Project'])->whereHas('Users', function ($q) use ($request) {
            if ($request->user_id) {

                $q->where('users.id', $request->user_id);
            }
        });

        $start = Carbon::parse($request->start_date)->format('Y-m-d');
        $end = Carbon::parse($request->end_date)->format('Y-m-d');

        if ($request->start_date || $request->end_date) {

            $qb->where(function ($query) use ($start, $end) {

                $query->WhereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere([
                        ['start_date', '<=', $start],
                        ['end_date', '>=', $end]
                    ]);
            });
        }
        $qb = QB::where($input, "id", $qb);
        $qb = QB::where($input, "project_id", $qb);
        $qb = QB::whereLike($input, "ceo_comment", $qb);
        $qb = QB::where($input, "status", $qb);

        $goals = $qb->paginate(5);



        return GoalResources::collection($goals)->additional(['meta' => [
            'Goals_String' => ($request->start_date) ? DateString::getDateString($start, $end, "Goals") : "Goals"
        ]]);
    }

    public function create(Request $request, Goal $goal)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'project_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required',
            'file' => 'required',
            'ceo_comment' => 'required',
            'status' => 'required',
            'user_id' => 'required'
        ]);

        $file = $request->file('file');

        $restult = FileHelper::saveFile($file, "GoalFiles");

        $goal = Goal::create([
            'project_id' => $data['project_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'],
            'file' => $restult,
            'ceo_comment' => $data['ceo_comment'],
            'status' => $data['status'],
            'isApproved' => 0,
        ]);

        $goal->Users()->sync($data['user_id']);

        $goal = Goal::where('id', $goal->id)->with(['Users', 'Project'])->first();
        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Goal')
            ]),
            'data' => GoalResources::make($goal)
        ];
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::where('id', $id)->with(['Users', 'Project'])->first();
        if (!$goal) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Goal')
            ]));
        }
        $file_path = $goal->file;
        $status = $goal->status;
        $end_date = $goal->end_date;

        $data = $request->all();

        $this->validateOrAbort($data, [
            'project_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required',
            'ceo_comment' => 'required',
            'status' => 'required',
            'isApproved' => 'required'
        ]);

        if ($request->file('file')) {

            FileHelper::deleteFileIfNotDefault($file_path);

            $file = $request->file('file');

            $file_path = FileHelper::saveFile($file, "Goal_Files");
        }

        $goal->update([
            'project_id' => $data['project_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'],
            'file' => $file_path,
            'ceo_comment' => $data['ceo_comment'],
            'status' => $data['status'],
            'isApproved' => $data['isApproved'],
        ]);

        if ($status != $data['status'] && Goal::$COMPLETED == $data['status']) {

            if ( $data['end_date'] == $end_date) {
                $sources = "Goal " . $data['status'] . " in time";
                $points = Setting::getValue('goal_completed');
            }

            if ( $data['end_date'] < $end_date) {

                $sources = "Goal " . $data['status'] . " before date";
                $points = Setting::getValue('goal_completed_before');
            }
            
            if ( $data['end_date'] > $end_date) {

                $sources = "Goal " . $data['status'] . " after date";
                $points = Setting::getValue('goal_completed_after');
            }

            $credit_point = CreditPoint::create([
                'user_id' => $data['user_id'],
                'sources' => $sources,
                'points' => $points,
            ]);
        }

        $goal->Users()->sync($data['user_id']);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Goal')
            ]),
            'data' => GoalResources::make($goal)
        ];
    }

    public function delete($id)
    {
        $goal = Goal::find($id);
        if (!$goal) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Goal')
            ]));
        }

        FileHelper::deleteFileIfNotDefault($goal->file);

        $goal->delete();

        GoalUser::where('goal_id', $id)->delete();

        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Goal')
            ]),
        ];
    }


}