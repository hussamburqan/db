<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NClinic extends Model
{
    protected $table = 'nclinics';
    
    protected $fillable = [
        'user_id',
        'major_id',
        'location',
        'photo',
        'cover_photo',
        'description',
        'opening_time',
        'closing_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'nclinic_id');
    }
}