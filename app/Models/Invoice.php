<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [

        'amount',
        'payment_method',
        'payment_status',
        'time',
        'user_id',   
        'patient_id',   
        'doctor_id',   
        'nclinic_id'   
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function nclinic()
    {
        return $this->belongsTo(NClinic::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
