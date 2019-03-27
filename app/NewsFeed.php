<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model{
    protected $table = 'news_feeds';
    protected $guarded = ['id'];
}
