<?php

Route::group(['middleware' => ['api', 'auth:api'], 'prefix' => $v1Prefix . '/admin'], function () {

    // settings
    Route::get('settings', 'AdminSettingsController@index');
    Route::put('settings', 'AdminSettingsController@edit');

    // permissions
    Route::get('permissions', 'AdminPermissionController@index');
    Route::get('permissions/role/{id}', 'AdminPermissionController@rolePermissions');
    Route::get('permissions/user/{id}', 'AdminPermissionController@userPermissions');
    Route::post('permissions', 'AdminPermissionController@create');
    Route::patch('permissions/role/{id}', 'AdminPermissionController@updatePermissions');
        
    // Role
    Route::get('roles', 'AdminRoleController@index');
    Route::get('roles/user/{id}', 'AdminRoleController@userRoles');
    Route::post('roles', 'AdminRoleController@create');
    Route::patch('roles/user/{id}', 'AdminRoleController@updateRoles');

    // StatementController
    Route::get('/statements', 'AdminStatementController@index');
    Route::post('/statements', 'AdminStatementController@create');
    Route::post('/statements/update/{id}', 'AdminStatementController@update');
    Route::delete('/statements/{id}', 'AdminStatementController@delete');

    // GoalController
    Route::get('/goals', 'AdminGoalController@index');
    Route::post('/goals', 'AdminGoalController@create');
    Route::post('/goals/update/{id}', 'AdminGoalController@update');
    Route::delete('/goals/{id}', 'AdminGoalController@delete');

    // ComanyController
    Route::get('/companies', 'AdminCompanyController@index');
    Route::post('/companies', 'AdminCompanyController@store');
    Route::patch('/companies/{id}', 'AdminCompanyController@update');
    Route::delete('/companies/{id}', 'AdminCompanyController@delete');

    //  AdminEmployeeController
    Route::get('/employees', 'AdminEmployeeController@index');
    Route::post('/employees', 'AdminEmployeeController@create');
    Route::post('/employees/update/{id}', 'AdminEmployeeController@update');
    Route::delete('/employees/{id}', 'AdminEmployeeController@delete');

    // ProjectsController
    Route::get('/projects', 'AdminProjectController@index');
    Route::get('/projects/details/{id}', 'AdminProjectController@showSingleProjectDetails');
    Route::post('/projects', 'AdminProjectController@create');
    Route::post('/projects/update/{id}', 'AdminProjectController@update');
    Route::delete('/projects/{id}', 'AdminProjectController@delete');

    //  TimeLineController
    Route::get('/timelines', 'AdminTimelineController@index');
    Route::patch('/timelines/{id}', 'AdminTimelineController@update');
    Route::delete('/timelines/{id}', 'AdminTimelineController@delete');

    //  AdminLeaveController
    Route::get('/leaves', 'AdminLeaveController@index');
    Route::post('/leaves', 'AdminLeaveController@create');
    Route::post('/leaves/update/{id}', 'AdminLeaveController@update');
    Route::delete('/leaves/{id}', 'AdminLeaveController@delete');

    // AdminNewsFeedController
    Route::get('/news', 'AdminNewsFeedController@index');
    Route::post('/news', 'AdminNewsFeedController@store');
    Route::patch('/news/{id}', 'AdminNewsFeedController@update');
    Route::delete('/news/{id}', 'AdminNewsFeedController@delete');

    Route::get('/metaData', 'APISiteController@metaData');

});