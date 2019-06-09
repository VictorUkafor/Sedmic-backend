<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Slip extends Model
{

    use Notifiable, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'church_id',
        'campaign',
        'first_name',
        'last_name',
        'sex',
        'phone',
        'email',
        'address',
        'ministered_by',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
