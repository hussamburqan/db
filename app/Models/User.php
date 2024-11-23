<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'age',
        'blood_type',
        'gender',
        'phone'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patient()
{
    return $this->hasOne(Patient::class, 'user_id');
}

}