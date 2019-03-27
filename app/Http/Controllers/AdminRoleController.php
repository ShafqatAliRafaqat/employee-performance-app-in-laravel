<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\RoleResource;
use App\User;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller {

    protected $permissions = [
        'index'=>'role-list',
        'userRoles' => 'user-role-list',
        'create'=>'role-create',
        'updateRoles' => 'user-role-update'
    ];

    public function index(){
        return RoleResource::collection(Role::all());
    }

    public function userRoles($id){

        $user = User::find($id);

        if(!$user){
            abort(400,__('messages.model.not.found',[
                'model' => __('messages.User')
            ]));
        }

        return RoleResource::collection($user->roles);
    }

    public function create(Request $request){

        $input = $request->all();

        $this->validateOrAbort($input,[
            'name' => 'required'
        ]);

        $role = Role::create([
            'name'=>$input['name']
        ]);

        return [
            'message' => __('messages.model.create.success',[
                'model' => __('messages.Role')
            ]),
            'data' => RoleResource::make($role)
        ];

    }

    public function updateRoles(Request $request,$id){

        $input = $request->all();

        $this->validateOrAbort($input,[
            'roles' => 'present'
        ]);

        $user = User::find($id);

        if(!$user){
            abort(400,__('messages.model.not.found',[
                'model' => __('messages.User')
            ]));
        }

        $user->syncRoles($input['roles']);

        return ['message' => __('messages.user.roles.updated')];
    }
}
