<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'message',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class);
    }
    public function reacts()
    {
        return $this->hasMany(PostReact::class);
    }
}
