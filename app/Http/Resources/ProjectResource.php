<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\APIAuthController;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return self::Project($this);
    }

    public static function Project($project){

        $data = [
            'id' => $project->id,
            'name' => $project->name,
            'start_date' => $project->start_date,
            'end_date'=>$project->end_date,
            'progress'=>$project->progress,
            'status'=>$project->status,
            'detail_file'=> url($project->detail_file),
            'prof_and_loss'=> url($project->prof_and_loss),
            'company_id'=>$project->company_id,
            'client_comment'=>$project->client_comment,
            'ceo_comment'=>$project->ceo_comment,
            'user_remarks'=>($project->pivot)? $project->pivot->user_remarks:"",
            'Company' =>CompanyResource::make($project->whenLoaded('Company')),
            'Users' => UserResource::collection($project->whenLoaded('Users')),
            'created_at'=>$project->created_at->diffForHumans(),
            'updated_at'=>$project->updated_at->diffForHumans()
        ];

        return $data;
    }
}
