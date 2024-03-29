<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AggregateExecutive extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'aggregate_id',
        'member_id',
        'position',
        'updated_by',
        'created_by',
        'deleted_by',
    ];
}
