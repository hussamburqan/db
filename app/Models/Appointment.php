<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;


    protected $fillable = [
        'date',
        'time',
        'description',
        'status',
        'patient_id',
        'nclinic_id',
        'doctor_id',
        'user_id'
    ];

    public function nclinic()
    {
        return $this->belongsTo(NClinic::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Define the relationship with the Patient model
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
