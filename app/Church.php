<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{

    /**
     * Get the users of the church.
     */
    public function users()
    {
        return $this->hasMany('App\User', 'church_username', 'username');
    }


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
     * Get the slips of the church.
     */
    public function slips()
    {
        return $this->hasMany('App\Slip');
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
     * Get the programmes of the church.
     */
    public function programmes()
    {
        return $this->hasMany('App\Programme', 'church_id');
    }


    /**
     * Get the incomes of the church.
     */
    public function incomes()
    {
        return $this->hasMany('App\Income');
    }


    /**
     * Get the income types of the church.
     */
    public function incomeTypes()
    {
        return $this->hasMany('App\IncomeType');
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
