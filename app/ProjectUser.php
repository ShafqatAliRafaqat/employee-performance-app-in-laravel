<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    protected $table = 'project_user';
    protected $fillable = [
        'user_id', 'project_id', 'user_remarks',
    ];
}
