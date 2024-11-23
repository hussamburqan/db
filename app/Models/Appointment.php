<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'date', 
        'time', 
        'description', 
        'status', 
        'instructions', 
        'patient_id',
        'doctor_id', 
        'nclinic_id'
    ];

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function doctor()
{
    return $this->belongsTo(Doctor::class);
}

public function patient()
{
    return $this->belongsTo(Patient::class);
}

public function nclinic()
{
    return $this->belongsTo(NClinic::class);
}

}
