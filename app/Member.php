<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use Notifiable, SoftDeletes;

    /**
     * Get the units of the member.
     */
    public function units()
    {
        return $this->belongsToMany('App\Unit');
    }

    /**
     * Get the unit positions of the member.
     */
    public function unitPositions()
    {
        return $this->hasMany('App\UnitExecutive');
    }


    /**
     * Get the aggregate positions of the member.
     */
    public function AggregatePositions()
    {
        return $this->hasMany('App\AggregateExecutive');
    }

    /**
     * Get the church that owns the member.
     */
    public function church()
    {
        return $this->belongsTo('App\Church');
    }


    /**
     * Get the firstTimers of the member.
     */
    public function firstTimers()
    {
        return $this->hasMany('App\FirstTimer', 'invited_by');
    }


    /**
     * Get the slips of the member.
     */
    public function slips()
    {
        return $this->hasMany('App\Slip', 'ministered_by');
    }


    /**
     * Get the invitees that are member.
     */
    public function invitees()
    {
        return $this->hasMany('App\Invitee', 'member_id');
    }


    /**
     * Get the givings of the member.
     */
    public function givings()
    {
        return $this->hasMany('App\Income', 'member_id');
    }

    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'church_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'marital_status',
        'phone',
        'address',
        'image',
        'date_of_birth',
        'occupation',
        'email',
        'image',
        'birthday',
        'age_category',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}


