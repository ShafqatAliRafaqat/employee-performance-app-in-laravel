<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    public function run()
    {
        // permissions for Admin

        $prsArray = [
            'role-list',            'user-role-list',           'role-create',          'user-role-update',
            'permission-list',      'role-permission-list',     'user-permission-list', 'permission-create',
            'user-list-all',        'user-list',                'user-edit',            'role-permission-update',
            'user-delete',          'setting-list',             'setting-edit',         'selection-list',
           
            //For Company
            'company-list',         'company-create',           'company-update',       'company-delete',
           
            //For Employee
            'employee-list',       'employee-create',          'employee-edit',        'employee-update',
            'employee-delete',
           
            //For Goals
            'goal-list',           'goal-add',                  'goal-create',           'goal-userremarks',            
            'goal-edit',           'goal-update',               'goal-delete',

            //For Projects
            'project-list',        'project-add',              'project-create',       'project-showSingleProjectDetails',
            'project-edit',        'project-update',           'project-userremarks',  'project-delete',
            
            //For TimeLine
            'timeline-list',       'timeline-edit',            'timeline-update',       'timeline-delete',
            
            //For Leave
            'leave-list',           'leave-create',             'leave-update',         'leave-delete',
            
            //For Statements
            'statement-list',      'statement-create',         'statement-update',      'statement-delete',
            
            //For NEWS Feed
            'news-list',            'news-create',              'news-update',          'news-delete'
        ];

        foreach ($prsArray as $p) {
            $prs[] = Permission::findOrCreate($p);
        }

        $role = Role::create(['name' => env('SUPER_ADMIN_ROLE_NAME',"Admin")]);

        $role->syncPermissions($prs);

        // permissions for Employee

        $prsArray = [
            'user-permission-list',
            'isEmployee'
        ];

        $prs = [];

        foreach ($prsArray as $p) {
            $prs[] = Permission::findOrCreate($p);
        }

        $role = Role::create(['name' => "Employee"]);

        $role->syncPermissions($prs);
    }
}
