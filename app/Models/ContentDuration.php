<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentDuration extends Model
{
    protected $fillable = ['length'];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
