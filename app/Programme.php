<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{

    use SoftDeletes;

    /**
     * Get the invitees of the programme.
     */
    public function invitees()
    {
        return $this->hasMany('App\Invitee');
    }

    /**
     * Get the firstTimers of the programme.
     */
    public function firstTimers()
    {
        return $this->hasMany('App\FirstTimer');
    }


    /**
     * Get the signs of the programme.
     */
    public function signs()
    {
        return $this->hasMany('App\Sign');
    }


    /**
     * Get the handlers of the programme.
     */
    public function handlers()
    {
        return $this->hasMany('App\Handler', 'programme_id');
    }


    /**
     * Get the order of service of the programme.
     */
    public function orderOfServices()
    {
        return $this->hasMany('App\OrderOfService');
    }


    /**
     * Get the order of incomes of the programme.
     */
    public function incomes()
    {
        return $this->hasMany('App\Income', 'programme_id');
    }

    protected $guarded = ['id'];

    protected $fillable = [
        'church_id',
        'unit_id',
        'aggregate_id',
        'title',
        'type_of_meeting',
        'date',
        'venue',
        'time_starting',
        'time_ending',
        'ministered_by',
        'live',
        'report',
        'email_notification',
        'sms_notification',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
