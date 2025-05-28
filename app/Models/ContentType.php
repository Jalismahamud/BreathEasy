<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $fillable =['title'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
