<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FirstTimer extends Model
{

    use Notifiable, SoftDeletes;

    /**
     * Get the attendances that are first_timer.
     */
    public function attendances()
    {
        return $this->hasMany('App\Invitee', 'first_timer_id');
    }

    /**
     * Get the givings of the firstTimer.
     */
    public function givings()
    {
        return $this->hasMany('App\Income', 'first_timer_id');
    }

    protected $guarded = ['id'];

    protected $fillable = [
        'church_id',
        'programme_id',
        'first_name',
        'last_name',
        'sex',
        'phone',
        'email',
        'address',
        'invited_by',
        'image',
        'moved',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
