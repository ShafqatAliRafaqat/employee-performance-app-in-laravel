<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public static $ONGING = 'ongoing';
    public static $COMPLETED = 'completed';
    public static $UPCOMING = 'upcoming';
    public static $CANCELED = 'canceled';
    public static $PUSED = 'pused';

    public static $STATUS = [
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'upcoming' => 'Upcoming',
        'canceled' => 'Canceled',
        'pused' => 'Pused'
    ];

    protected $fillable = [
        'name', 'start_date', 'end_date', 'detail_file', 'progress', 'status', 'team', 'client_comment', 'ceo_comment', 'company_id', 'prof_and_loss',
    ];


    public function Company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function Goals()
    {
        return $this->hasMany('App\Goal');
    }
    
    public function Users()
    {
        return $this->belongsToMany('App\User','project_user','project_id','user_id')
        ->withPivot(['user_id', 'project_id', 'user_remarks'])->withTimestamps();
    }


}
