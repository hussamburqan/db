<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'name', 
        'description', 'is_active'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function nclinics()
    {
        return $this->hasMany(NClinic::class);
    }
}