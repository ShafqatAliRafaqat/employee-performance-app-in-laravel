<?php

// public api

Route::group(['middleware' => 'api', 'prefix' => $v1Prefix.'/auth'], function () {
   
    Route::post('login', 'LoginController@login');
    
});