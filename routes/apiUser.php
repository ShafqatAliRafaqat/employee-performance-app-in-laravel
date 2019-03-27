<?php

// secured api using user's access token

Route::group(['middleware' => ['api','auth:api'], 'prefix' => $v1Prefix.'/user'], function () {
    // auth
    

    Route::get('/company','ApiUserController@company');

    Route::get('/employee','ApiUserController@employee');

    Route::get('/projects','ApiUserController@projects');

    Route::get('/timelines','ApiUserController@timelines');

    Route::get('/goals','ApiUserController@goals');

    Route::get('/leaves','ApiUserController@leaves');
    
    Route::get('/news','ApiUserController@news');

    Route::get('/dashboard/employee_points', 'AdminDashboardController@index');

    Route::post('/goals/edit/{id}','ApiUserController@editGoal');

    Route::post('/project/remarks/{id}','ApiUserController@projectremarks');

    Route::post('/logout', 'Auth\APIAuthController@logout');

});
