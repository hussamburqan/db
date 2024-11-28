<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Doctor extends Model
{
    protected $fillable = [
        'experience_years',
        'specialization',
        'education',
        'photo',
        'start_work_time',
        'end_work_time',
        'default_time_reservations',
        'bio',
        'user_id',
        'major_id',
        'nclinic_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function clinic()
    {
        return $this->belongsTo(NClinic::class, 'nclinic_id');
    }

    public function archives()
    {
        return $this->hasMany(PatientArchive::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class);
    }
}