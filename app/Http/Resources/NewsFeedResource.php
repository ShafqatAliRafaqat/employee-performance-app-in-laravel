<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsFeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return self::NewsFeed($this);
    }

    public static function NewsFeed($news){

        $data = [
            'id' => $news->id,
            'news' => $news->news,
            'created_at'=>$news->created_at->diffForHumans(),
            'updated_at'=>$news->updated_at->diffForHumans()

        ];

        return $data;
    }
}
