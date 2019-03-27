<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{

    public static $ONGOING = 'ongoing';
    public static $COMPLETED = 'completed';
    public static $UPCOMING = 'upcoming';
    public static $CANCELED = 'canceled';
    public static $PUSED = 'pused';

    public static $STATUS = [
        'upcoming' => 'Upcoming',
        'ongoing' => 'Ongoing',
        'pused' => 'Pused',
        'completed' => 'Completed',
        'canceled' => 'Canceled',
    ];

    protected $fillable = [
        'project_id', 'start_date', 'end_date', 'file', 'description', 'status','isApproved', 'submission_time', 'ceo_comment',
    ];

    public function Project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }
    
    public function Users(){
        return $this->belongsToMany('App\User', 'goal_user', 'goal_id','user_id')
        ->withPivot(['goal_id', 'user_id', 'user_remarks'])->withTimestamps();
    }
}
