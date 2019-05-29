<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Aggregate extends Model
{
    use SoftDeletes;

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
        'level',
        'sub_unit_type',
        'handlers',
        'description',
        'image',
        'aggregate_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function aggregate()
    {
        return $this->belongsTo('App\Aggregate', 'aggregate_id');
    }

    public function subs()
    {
        return $this->hasMany('App\Aggregate', 'aggregate_id');
    }

    public function units()
    {
        return $this->hasMany('App\Unit', 'aggregate_id');
    }

}
