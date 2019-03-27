<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\APIAuthController;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class StatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    
    public function toArray($request) {
        return self::Statement($this);
    }

    public static function Statement($statement){

        $data = [
            'id' => $statement->id,
            'company_id' => $statement->company_id,
            'company' => $statement->Company->name,
            'month' => $statement->month,
            'year'=>$statement->year,
            'quadrant'=>$statement->quadrant,
            'file'=>url($statement->file),
            'created_at'=>$statement->created_at->diffForHumans(),
            'updated_at'=>$statement->updated_at->diffForHumans()

        ];

        return $data;
    }
}
