<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];

    
    protected $fillable = [
        'church_id',
        'income_type_id',
        'programme_id',
        'title',
        'type',
        'format',
        'amount',
        'member',
        'default_currency',
        'paid_currency',
        'prize',
        'group',
        'cash',
        'created_by',
        'updated_by',
        'deleted_by',
    ];



}
