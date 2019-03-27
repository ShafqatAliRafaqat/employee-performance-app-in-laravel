<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    public static $LATEPENALTY = 'came three day late in office';
    
    protected $fillable = [
        'login_time', 'logout_time', 'date', 'hours_absent','hours_worked','user_id'
    ];
    public function Users()
    {
        return $this->belongsTo('App\User');
    }
}
