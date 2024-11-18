<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        //'doctor_id'
    ];

    // public function doctor()
    // {
    //     return $this->belongsTo(Doctor::class);
    // }
}
