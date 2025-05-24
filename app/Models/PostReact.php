<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReact extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'like',
        'comment',
        'parent_comment_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(PostReact::class, 'parent_comment_id');
    }

    public function replies()
    {
        return $this->hasMany(PostReact::class, 'parent_comment_id');
    }
}
