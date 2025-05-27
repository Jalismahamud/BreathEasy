<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterIntake extends Model
{
    protected $fillable = ['user_id', 'amount', 'date'];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
