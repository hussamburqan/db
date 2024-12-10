<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientArchive extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'time', 'description', 'status', 'instructions', 'reservation_id', 'doctor_id'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'reservation_id', 'id'); 
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
