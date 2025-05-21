<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'phone',
        'email',
        'address',
        'opening_days',
        'opening_hours',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
