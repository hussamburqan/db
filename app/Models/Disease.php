<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseases extends Model
{
    use HasFactory;

    protected $fillable = [

        'disease_classification',
        'disease_type',
        'description',
        'major_id',
        'patient_id',
        'doctor_id'
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
