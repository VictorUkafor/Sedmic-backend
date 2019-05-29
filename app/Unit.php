<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use SoftDeletes;
    
    /**
     * Get the members of the unit.
     */
    public function members()
    {
        return $this->belongsToMany('App\Member')
        ->withTimestamps();
    }

    /**
     * Get the church that owns this unit.
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
        'name',
        'type',
        'handlers',
        'description',
        'image',
        'aggregate_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
