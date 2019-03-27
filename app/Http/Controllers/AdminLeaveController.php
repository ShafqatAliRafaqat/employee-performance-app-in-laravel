<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leave;
use App\Http\Controllers\Controller;
use App\Helpers\QB;
use App\Http\Resources\LeaveResource as LeaveResource;
use App\Setting;
use Carbon\Carbon;

class AdminLeaveController extends Controller
{
    protected $permissions = [
        'index' => 'leave-list',
        'store' => 'leave-create',
        'update' => 'leave-update',
        'destroy' => 'leave-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

        $start_year = Carbon::now()->startOfYear()->toDateTimeString();
        $end_year = Carbon::now()->endOfYear()->toDateTimeString();
       
        if ($request->start_date) {
            $start_year = Carbon::parse($request->start_date)->toDateTimeString();
            $end_year = Carbon::parse($request->end_date)->toDateTimeString();
        }

        $qb = Leave::orderBy('created_at', 'DESC')->whereBetween('leave_date',array($start_year,$end_year))->where('user_id', $request->user_id);

        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "detail", $qb);
        $qb = QB::where($input, "isTracked", $qb);
        $qb = QB::where($input, "isFull", $qb);
        $qb = QB::whereLike($input, "leave_type", $qb);

        $leave = $qb->paginate();

        return LeaveResource::collection($leave);
    }
  
    public function create(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'user_id' => 'required',
            'detail' => 'required',
            'isTracked' => 'required',
            'isFull' => 'required',
            'leave_type' => 'required',
            'leave_date' => 'required'
        ]);

        $leave = Leave::create([

            'user_id' => $data['user_id'],
            'detail' => $data['detail'],
            'isTracked' => $data['isTracked'],
            'isFull' => $data['isFull'],
            'leave_type' => $data['leave_type'],
            'leave_date' => $data['leave_date'],

        ]);

        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Leave')
            ]),
            'data' => LeaveResource::make($leave)
        ];
    }
    
    public function update(Request $request, $id, Leave $leave)
    {
        $leave = Leave::find($id);
        if (!$leave) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Leave')
            ]));
        }

        $data = $request->all();

        $this->validateOrAbort($data, [
            // 'user_id' => 'required',
            'detail' => 'required',
            'isTracked' => 'required',
            'isFull' => 'required',
            'leave_type' => 'required',
            'leave_date' => 'required'
        ]);

        $leave->update([
            // 'user_id' => $data['user_id'],
            'detail' => $data['detail'],
            'isTracked' => $data['isTracked'],
            'isFull' => $data['isFull'],
            'leave_type' => $data['leave_type'],
            'leave_date' => $data['leave_date'],
        ]);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Leave')
            ]),
            'data' => LeaveResource::make($leave)
        ];
    }

    public function delete(Leave $leave, $id)
    {
        $leave = Leave::find($id);
        if (!$leave) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.Leave')
            ]));
        }
        $leave->delete();
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Leave')
            ])
        ];
    }
}
