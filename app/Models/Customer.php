<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'document',
        'type',
        'email',
        'phone',
        'state_registration',
        'address',
        'number',
        'neighborhood',
        'city',
        'state',
        'zip_code',
    ];
}
