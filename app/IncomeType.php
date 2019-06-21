<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IncomeType extends Model
{

    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the order of incomes of the incomeType.
     */
    public function incomes()
    {
        return $this->hasMany('App\Income', 'income_type_id');
    }
    
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
