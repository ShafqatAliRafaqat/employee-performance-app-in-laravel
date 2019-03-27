<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\APIAuthController;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return self::Company($this);
    }

    public static function Company($company){

        $data = [
            'id' => $company->id,
            'name' => $company->name,
            'description' => $company->description,
            'created_at'=>$company->created_at->diffForHumans(),
            'updated_at'=>$company->updated_at->diffForHumans()

        ];

        return $data;
    }
}
