<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use SoftDeletes;

    /**
     * Get the units of the member.
     */
    public function units()
    {
        return $this->belongsToMany('App\Unit');
    }

    /**
     * Get the unit positions of the member.
     */
    public function unitPositions()
    {
        return $this->hasMany('App\UnitExecutive');
    }


    /**
     * Get the aggregate positions of the member.
     */
    public function AggregatePositions()
    {
        return $this->hasMany('App\AggregateExecutive');
    }

    /**
     * Get the church that owns the member.
     */
    public function church()
    {
        return $this->belongsTo('App\Church');
    }


    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'church_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'marital_status',
        'phone',
        'address',
        'image',
        'date_of_birth',
        'occupation',
        'email',
        'image',
        'birthday',
        'age_category',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}


