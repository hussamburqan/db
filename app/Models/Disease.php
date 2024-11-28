<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = [
        'name', 
        'classification', 
        'type', 
        'description',
        'symptoms', 
        'treatment_protocol', 
        'doctor_id', 
        'patients_id'

    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patients_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

   
}
