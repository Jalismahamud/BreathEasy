<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $fillable =['title'];

    protected $hidden = ['created_at', 'updated_at'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
