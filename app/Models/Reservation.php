<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'date', 
        'time', 
        'duration_minutes', 
        'status',
        'reason_for_visit', 
        'notes', 
        'patient_id',
        'doctor_id', 
        'nclinic_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function nclinic()
    {
        return $this->belongsTo(NClinic::class);
    }

    public function disease()
    {
        return $this->hasOne(Disease::class);
    }
}