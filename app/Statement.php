<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year', 'month', 'file', 'company_id','quadrant',
    ];
    public function Company()
    {
        return $this->belongsTo('App\Company','company_id');
    }

}
