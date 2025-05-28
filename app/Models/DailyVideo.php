<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyVideo extends Model
{
    protected $fillable = ['video'];

    protected $hidden = ['created_at', 'updated_at'];
    
}
