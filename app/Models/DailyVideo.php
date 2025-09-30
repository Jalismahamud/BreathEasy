<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyVideo extends Model
{
    // Allow created_at to be set when assigning videos to specific dates
    protected $fillable = ['video', 'created_at'];

    // Keep timestamps visible so views/controllers can read created_at
    protected $hidden = [];

}
