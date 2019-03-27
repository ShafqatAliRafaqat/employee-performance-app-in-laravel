<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return self::Goal($this);
    }

    public static function Goal($goal) {
        

        $userRemarkts = "";

        if($goal->Users){
            if(sizeof($goal->Users) > 0){
                $userRemarkts = $goal->Users[0]->pivot->user_remarks;
            }
        }

        $data = [
            'id' => $goal->id,
            'project_id' => $goal->project_id,
            'start_date' => $goal->start_date,
            'end_date' => $goal->end_date,
            'description' => $goal->description,
            'file' => url($goal->file),
            'status' => $goal->status,
            'isApproved' => $goal->isApproved,
            'ceo_comment' => $goal->ceo_comment,
            'user_remarks'=>$userRemarkts,
            'Users' =>UserResource::collection($goal->whenLoaded('Users')),
            'Project' =>ProjectResource::make($goal->whenLoaded('Project')),
            'created_at' => $goal->created_at->diffForHumans(),
            'updated_at' => $goal->updated_at->diffForHumans()
        ];

        return $data;
    }

}
