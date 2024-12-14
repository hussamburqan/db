<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientArchive extends Model
{

    protected $fillable = [
        'date', 'description', 'status', 'instructions', 'reservation_id', 'doctor_id'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }    

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
