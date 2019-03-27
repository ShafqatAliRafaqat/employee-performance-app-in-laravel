<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource as UserResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use App\Setting;
use App\CreditPoint;
use Carbon\Carbon;
use App\Helpers\DateString;

class AdminEmployeeController extends Controller
{
    protected $permissions = [
        'index' => 'employee-list',
        'create' => 'employee-create',
        'edit' => 'employee-edit',
        'update' => 'employee-update',
        'delete' => 'employee-delete',
    ];

    public function index(Request $request)
    {
        $input = $request->all();

        $start = Carbon::parse($request->start_date)->toDateTimeString();
        $end = Carbon::parse($request->end_date)->toDateTimeString();

        $qb = User::orderBy('created_at', 'DESC')->with(['Company','CreditPoints' => function ($qb) use ($start, $end) {

            if (!$start && !$end) {

                $start = Carbon::now()->startOfMonth()->subMonth()->toDateTimeString();
                $end = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();
            }
            $qb->whereBetween('created_at', array($start, $end));
        }]);

        // $qb = User::whereHas('Projects', function ($q) use ($request) {
        //     if ($request->project_id) {

        //         $q->where('projects.id', $request->project_id);
        //     }
        // });

        $qb = QB::where($input, "id", $qb);
        $qb = QB::whereLike($input, "name", $qb);
        $qb = QB::whereLike($input, "email", $qb);
        $qb = QB::whereLike($input, "cnic", $qb);
        $qb = QB::whereLike($input, "phone", $qb);
        $qb = QB::whereLike($input, "address", $qb);
        $qb = QB::where($input, "company_id", $qb);
        $qb = QB::where($input, "employee_type", $qb);

        $users = $qb->paginate();

        return UserResource::collection($users)->additional(['meta' => [

            'Credit Points' => ($request->start_date) ? DateString:: getDateString($start, $end, "Credit Points") : "Credit Points"

        ]]);
    }
    public function create(Request $request)
    {
        $data = $request->all();

        $this->validateOrAbort($data, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'cnic' => 'required',
            'phone' => 'required',
            'joining' => 'required',
            'address' => 'required',
            'file' => 'required',
            'company_id' => 'required',
            'employee_type' => 'required'
        ]);

        $leaves_allowed = Setting::getValue('leaves_allowed');

        if ($request->leaves_allowed) {

            $leaves_allowed = $data['leaves_allowed'];
        }

        $file = $request->file('file');
        $restult = FileHelper::saveFile($file, "Employee_CV");

        $employeeSave = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cnic' => $data['cnic'],
            'phone' => $data['phone'],
            'employee_type' => $data['employee_type'],
            'joining' => $data['joining'],
            'address' => $data['address'],
            'cv' => $restult,
            'company_id' => $data['company_id'],
            'leaves_allowed' => $leaves_allowed,
        ]);

        return [
            'message' => __('messages.model.create.success', [
                'model' => __('messages.Employee')
            ]),
            'data' => UserResource::make($employeeSave)
        ];
    }
    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->with('Company')->first();

        if (!$user) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.User')
            ]));
        }

        $file_path = $user->cv;

        $email = $user->email;

        $data = $request->all();

        $this->validateOrAbort($data, [
            'name' => 'required',
            'cnic' => 'required',
            'phone' => 'required',
            'joining' => 'required',
            'address' => 'required',
            'company_id' => 'required',
            'employee_type' => 'required'
        ]);

        $leaves_allowed = Setting::getValue('leaves_allowed');

        if ($request->leaves_allowed) {

            $leaves_allowed = $data['leaves_allowed'];
        }

        if ($request->file('file')) {

            $file = $request->file('file');

            FileHelper::deleteFileIfNotDefault($file_path);

            $file_path = FileHelper::saveFile($file, "Employee_CV");

        }


        if ($data['email'] != $email) {

            $this->validateOrAbort($data, [
                'email' => 'required|email|unique:users'
            ]);

            $email = $data['email'];
        }

        $update = [
            'name' => $data['name'],
            'email' => $email,
            'cnic' => $data['cnic'],
            'phone' => $data['phone'],
            'employee_type' => $data['employee_type'],
            'joining' => $data['joining'],
            'address' => $data['address'],
            'cv' => $file_path,
            'company_id' => $data['company_id'],
            'leaves_allowed' => $leaves_allowed,
        ];

        if($request->password && $request->password!=""){
            $update['password'] =  Hash::make($data['password']);
        }

        $user->update($update);

        return [
            'message' => __('messages.model.edit.success', [
                'model' => __('messages.Employee')
            ]),
            'data' => UserResource::make($user)
        ];
    }
    public function delete(User $user, $id)
    {
        $user = User::where('id', $id)->with('CreditPoints')->first();
        if (!$user) {

            abort(400, __('messages.model.not.found', [
                'model' => __('messages.User')
            ]));
        }
        FileHelper::deleteFileIfNotDefault($user->cv);

        $user->delete();
        return [
            'message' => __('messages.model.delete.success', [
                'model' => __('messages.Employee')
            ])
        ];
    }
}
