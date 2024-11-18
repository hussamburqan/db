<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NClinic extends Model
{    
    protected $table = 'nclinics';

    protected $fillable = [
        'name',
        'location',
        'description',
        'start_date',
        'end_date',
        'status',
        'email',
        'phone',
        'major_id',
        'user_id',
        'patient_id'
    ];
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}