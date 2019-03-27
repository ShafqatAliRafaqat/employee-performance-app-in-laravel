<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Timeline;
use App\Setting;
use Illuminate\Support\Facades\DB;

class APIAuthController extends Controller
{

    public function logout()
    {
        $user_id = Auth::user()->id;
        $todaydate = Carbon::now()->toDateString();

        $loggedin_user = Timeline::where(['user_id' => $user_id, 'date' => $todaydate, 'logout_time' => null])->first();

        $logout_time = Carbon::now()->toTimeString();

        $loggedin_user->update([
            'logout_time' => $logout_time,
        ]);
        auth()->logout();

        return ['message' => __('messages.logout.success.message')];
    }

    public function refresh()
    {
        $token = auth()->refresh();
        return self::getTokenArray($token);
    }


    public static function getTokenArray($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
        ];
    }

}
