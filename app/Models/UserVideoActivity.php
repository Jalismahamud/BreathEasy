<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVideoActivity extends Model
{
    protected $fillable = [
        'user_id',
        'content_id',
        'watched_seconds',
        'progress',
        'completed',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
