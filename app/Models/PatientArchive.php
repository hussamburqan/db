<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientArchive extends Model
{
    protected $table = 'patient_archives';

    protected $fillable = [
        'date',
        'time',
        'description',
        'status',
        'instructions',
        'patient_id',
        'doctor_id'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}