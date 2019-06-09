<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Invitee extends Model
{
    use Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'programme_id',
        'member_id',
        'slip_id',
        'first_timer_id',
        'present',
        'first_name',
        'last_name',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
