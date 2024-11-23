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
        'reservation_id'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
