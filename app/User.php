<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{


    use HasRoles;
    use Notifiable;

    protected $guarded = ['id'];

    protected $guard_name = 'api';

    public static $TECHNICAL = 'technical';
    public static $BUSSINESS = 'business';

    public static $EMPLOYEE_TYPES = [
        'technical' => 'Technical Employee',
        'business' => 'Business Employee',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    // protected $fillable = [
    //     'name', 'email', 'password', 'creditPoint','cnic','joining','address','cv','leaves_allowed','user_role',
    //     'company_id','last_login_at','last_logout_at'
    // ];
    public function Projects()
    {
        return $this->belongsToMany('App\Project', 'project_user', 'user_id', 'project_id')
        ->withPivot(['user_id', 'project_id', 'user_remarks'])->withTimestamps();
    }
    public function Goals()
    {
        return $this->belongsToMany('App\Goal', 'goal_user', 'user_id', 'goal_id')
        ->withPivot(['user_id', 'goal_id', 'user_remarks'])->withTimestamps();
    }
    public function CreditPoints()
    {
        return $this->hasMany('App\CreditPoint');
    }
    public function Timelines()
    {
        return $this->hasMany('App\Timeline');
    }
    public function Company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }
    public function Leaves()
    {
        return $this->hasOne('App\Leave');
    }
    public function NewsFeeds()
    {
        return $this->hasMany('App\NewsFeed');
    }

}

