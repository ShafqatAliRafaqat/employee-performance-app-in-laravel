<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\ArrayHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\PermissionResource;

class AdminPermissionController extends Controller{
    
    protected $permissions = [
        'index'=>'permission-list',
        'rolePermissions'=>'role-permission-list',
        'userPermissions'=>'user-permission-list',
        'create'=>'permission-create',
        'updatePermissions'=>'role-permission-update',
    ];

    public function index(){
        $permissions = Permission::orderBy('name')->get();
        return PermissionResource::collection($permissions);
    }

    public function rolePermissions($id){

        $role = Role::find($id);

        if(!$role){
            abort(400,__('messages.model.not.found',[
                'model' => __('messages.Role')
            ]));
        }

        $permissions = $role->permissions()->orderBy('name')->get();

        return PermissionResource::collection($permissions);
    }

    public function userPermissions($id){

        $user = User::find($id);

        if(!$user){
            abort(400,__('messages.model.not.found',[
                'model' => __('messages.User')
            ]));
        }

        $permissions = [];

        foreach ($user->roles as $role){
            $prs = $role->permissions()->pluck('name')->toArray();
            $permissions = ArrayHelper::array_merge($permissions,$prs);
        }

        return $permissions;
    }

    public function create(Request $request){

        $input = $request->all();

        $this->validateOrAbort($input,[
            'name' => 'required'
        ]);

        $pr = Permission::create([
            'name'=>$input['name']
        ]);

        return [
            'message' => __('messages.model.create.success',[
                'model' => __('messages.Permission')
            ]),
            'data' => PermissionResource::make($pr)
        ];
    }

    public function updatePermissions(Request $request,$id){

        $input = $request->all();

        $this->validateOrAbort($input,[
            'permissions' => 'present'
        ]);

        $role = Role::find($id);

        if(!$role){
            abort(400,__('messages.model.not.found',[
                'model' => __('messages.Role')
            ]));
        }

        $role->syncPermissions($input['permissions']);

        return ['message' => __('messages.role.permissions.updated')];
    }
}
