<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'content_type_id',
        'content_duration_id',
        'type',
        'title',
        'description',
        'image',
        'video',
        'video_length',
        'is_premium'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

}


