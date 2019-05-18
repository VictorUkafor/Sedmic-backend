<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Church extends Model
{
    protected $fillable = [
        'name_of_church',
        'username',
        'official_email',
        'venue',
        'images',
        'minister_in_charge',
        'contact_numbers',
    ];

}
