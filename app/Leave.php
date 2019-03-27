<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    public static $INFORMED = 'Informed';
    public static $UNINFORMED = 'Uninformed';
    public static $FULLLEAVE = 'full_leave';
    public static $HELFLEAVE = 'half_leave';
    public static $DETAIL = 'is absent today';
    public static $LATEPENALTY = 'is absent because you are come late three days';

    public static $LEAVE_TYPES = [
        'Informed' => 'Informed',
        'Uninformed' => 'Uninformed',
    ];
    public static $ISFULL = [
        'full_leave' => 'Full Leave',
        'half_leave' => 'Half Leave',
    ];
    protected $fillable = [
        'detail', 'isTracked', 'isFull', 'leave_date', 'leave_type','user_id'
    ];
    public function User()
    {
        return $this->hasOne('App\User');
    }
}
