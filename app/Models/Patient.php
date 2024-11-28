<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'medical_history',
        'allergies',
        'blood_type',
        'current_medications',
        'medical_recommendations',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function archives()
    {
        return $this->hasMany(PatientArchive::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class, 'patients_id'); 
    }
}