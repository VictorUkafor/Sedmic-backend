<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sign extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'programme_id',
        'invitee_id',
        'value',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
