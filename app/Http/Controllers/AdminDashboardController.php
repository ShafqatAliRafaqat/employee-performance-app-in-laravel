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
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $start = Carbon::now()->startOfMonth()->toDateTimeString();
        $end = Carbon::now()->endOfMonth()->toDateTimeString();

        if ($request->start_date) {

            $start = Carbon::parse($request->start_date)->toDateTimeString();
            $end = Carbon::parse($request->end_date)->toDateTimeString();
        }

        $user = DB::select(DB::raw("SELECT users.id, users.name, users.employee_type, sum(credit_points.points) AS points
        FROM users 
        LEFT JOIN credit_points ON users.id = credit_points.user_id
        WHERE (credit_points.created_at >= '$start' AND credit_points.created_at <= '$end') OR points is null 
        GROUP BY users.name order by points DESC"));

        return $user;
    }
}
