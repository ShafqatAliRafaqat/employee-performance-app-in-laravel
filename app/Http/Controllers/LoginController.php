<?php

namespace App\Http\Controllers;

use App\Timeline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request) {

        $input = $request->all();

        $this->validateOrAbort($input,[
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        
        if (! $token = auth()->attempt($input)) {
            abort(401,__('messages.login.error.message'));
        }

        $user = Auth::user();
        
        Timeline::create([
                
            'user_id'=>$user->id,     
            'login_time'=>Carbon::now(),
            'date'=>Carbon::now()->toDateString(),
                   
        ]);

        $user->token = $token;
        $payload = UserResource::make($user)->toArray($request);

        return response()->json(array_merge(['message'=>__('messages.login.success.message')],$payload));
    }

}
