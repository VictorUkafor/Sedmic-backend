<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitExecutive extends Model
{
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_id',
        'member_id',
        'position',
        'updated_by',
        'created_by',
        'deleted_by',
    ];

}
