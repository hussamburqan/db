<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'experience_years', 
        'specialization', 
        'education', 
        'bio',
        'major_id', 
        'n_clinic_id',
        'photo'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function nclinic()
    {
        return $this->belongsTo(NClinic::class, 'nclinic_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}