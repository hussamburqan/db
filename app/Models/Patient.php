<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'name',
        'phone',
        'address',
        'age',
        'email',
        'blood_type',
        'gender',
        'disease_type',
        'medical_history',
        'medical_recommendations',
        //'user_id',
        //'nclinic_id',
        //'doctor_id'
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

    public function nclinic()
    {
        return $this->belongsTo(NClinic::class);
    }

    public function doctor()
    {
        return $this->belongsTo(NClinic::class);
    }

    // public function user()
    // {
    //     return $this->belongsTo(NClinic::class);
    // }


}
