<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IncomeType extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];

    
    protected $fillable = [
        'church_id',
        'name',
        'format',
        'currency',
        'prize',
        'group',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


}
