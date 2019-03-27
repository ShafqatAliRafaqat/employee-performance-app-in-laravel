<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'description',
    ];
    
    public function Projects()
    {
        return $this->hasMany('App\Project');
    }
    public function Statements()
    {
        return $this->hasMany('App\Statement');
    }
    public function Users()
    {
        return $this->hasMany('App\User');
    }
}
