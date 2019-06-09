<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Handler extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'programme_id',
        'unit_id',
        'aggregate_id',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
