<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title' , 'image' , 'statusP'];

    protected $hidden = ['created_at' , 'updated_at'];
}
