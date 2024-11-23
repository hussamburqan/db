<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'emergency_contact', 
        'emergency_phone', 
        'medical_history',
        'allergies', 
        'current_medications', 
        'medical_recommendations', 
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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