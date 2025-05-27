<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterGoal extends Model
{
    protected $fillable = ['user_id', 'goal'];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
