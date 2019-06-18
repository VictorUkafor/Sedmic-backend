<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderOfService extends Model
{
    use SoftDeletes;


    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'programme_id',
        'invitee_id',
        'title',
        'start_time',
        'end_time',
        'actual_start',
        'actual_end',
        'duration',
        'order',
        'instruction',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
