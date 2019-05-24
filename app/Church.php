<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{

    /**
     * Get the members of the church.
     */
    public function members()
    {
        return $this->hasMany('App\Member');
    }


    /**
     * Get the units of the church.
     */
    public function units()
    {
        return $this->hasMany('App\Unit');
    }


    /**
     * Get the aggregates of the church.
     */
    public function aggregates()
    {
        return $this->hasMany('App\Aggregate');
    }

    protected $guarded = ['id'];

    
    protected $fillable = [
        'name_of_church',
        'username',
        'official_email',
        'venue',
        'images',
        'minister_in_charge',
        'contact_numbers',
    ];

}
