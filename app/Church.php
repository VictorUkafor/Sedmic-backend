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
     * Get the first timers of the church.
     */
    public function firstTimers()
    {
        return $this->hasMany('App\FirstTimer');
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


    /**
     * Get the programme of the church.
     */
    public function Programmes()
    {
        return $this->hasMany('App\Programme', 'church_id');
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
        'sms_sender_name'
    ];

}
