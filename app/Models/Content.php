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
        'video',
        'video_length',
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


