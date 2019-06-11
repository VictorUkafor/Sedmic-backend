<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{

    use SoftDeletes;

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
        'email_notification',
        'sms_notification',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
