<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return self::Timeline($this);
    }

    public static function Timeline($timeline){

        $data = [
            'id' => $timeline->id,
            'date' => $timeline->date,
            'login_time' => $timeline->login_time,
            'logout_time'=>$timeline->logout_time,
            'hours_worked'=>$timeline->hours_worked,
            'hours_absent'=>$timeline->hours_absent,
            'user_id' => $timeline->user_id,
            'created_at'=>$timeline->created_at->diffForHumans(),
            'updated_at'=>$timeline->updated_at->diffForHumans()
        ];

        return $data;
    }
}
