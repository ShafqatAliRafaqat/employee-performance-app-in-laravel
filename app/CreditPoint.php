<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditPoint extends Model
{
    protected $fillable = [
        'user_id', 'points', 'sources'
    ];
    public function User()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
