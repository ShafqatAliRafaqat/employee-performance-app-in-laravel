<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\APIAuthController;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Passport\Bridge\User;
use App\Setting;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return self::toUser($this);
    }

    public static function toUser($u)
    {

        $credit = $u->CreditPoints;
       
        $CreditPoints = $credit->sum('points');
       
        $data = [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'cnic' => $u->cnic,
            'phone' => $u->phone,
            'employee_type' => $u->employee_type,
            'joining' => $u->joining,
            'address' => $u->address,
            'cv' => url($u->cv),
            'leaves_allowed' => $u->leaves_allowed,
            'user_remarks'=>($u->pivot)? $u->pivot->user_remarks:"",
            'company_id' => $u->company_id,
            'Company' => $u->Company,
            'credit_points' => $CreditPoints,
            'created_at' => $u->created_at->diffForHumans(),
            'updated_at' => $u->updated_at->diffForHumans()
        ];

        if (Auth::check() && isset($u->token)) {
            $token = APIAuthController::getTokenArray($u->token);
            $data = array_merge($data, $token);
        }

        return $data;
    }
}
