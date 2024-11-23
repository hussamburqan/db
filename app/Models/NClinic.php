<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NClinic extends Model
{    
    protected $table = 'nclinics';
    protected $fillable = [
        'name',
        'location',
        'description',
        'opening_time',
        'closing_time',
        'status',
        'email',
        'phone',
        'major_id',
        'photo'
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}