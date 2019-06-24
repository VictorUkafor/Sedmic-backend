<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Slip extends Model
{

    use Notifiable, SoftDeletes;

    /**
     * Get the invitees that are slip.
     */
    public function invitees()
    {
        return $this->hasMany('App\Invitee', 'slip_id');
    }

    /**
     * Get the givings of the slip.
     */
    public function givings()
    {
        return $this->hasMany('App\Income', 'slip_id');
    }

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
        'moved',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

}
