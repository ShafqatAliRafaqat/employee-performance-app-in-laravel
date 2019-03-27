<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoalUser extends Model
{
    protected $table = 'goal_user';
    protected $fillable = [
        'user_id', 'goal_id', 'user_remarks',
    ];
}
