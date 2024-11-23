<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalNews extends Model
{
    protected $fillable = [
        'title', 
        'content', 
        'image', 
        'category',
        'is_featured',
        'is_active'
    ];
}
