<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 
        'amount', 
        'payment_method', 
        'payment_status',
        'paid_at', 
        'notes', 
        'appointment_id', 
        'patient_id',
        'doctor_id', 
        'nclinic_id'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

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
}
