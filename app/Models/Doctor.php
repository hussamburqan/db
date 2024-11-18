<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'age',
        'email',
        'gender',
        'phone',
        'specialization',
        'experience',
        'education',
        'major_id',
        'nclinic_id',
        'user_id',
        'patient_id'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function resrvations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class);
    }

    public function majors()
    {
        return $this->hasMany(Major::class);
    }

    public function nclinics()
    {
        return $this->hasMany(NClinic::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
