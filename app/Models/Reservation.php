<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
        'date',
        'email',
        'duration',
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

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
