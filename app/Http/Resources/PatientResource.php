<?php
// app/Http/Resources/PatientResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'age' => $this->age,
            'email' => $this->email,
            'blood_type' => $this->blood_type,
            'gender' => $this->gender,
            'disease_type' => $this->disease_type,
            'medical_history' => $this->medical_history,
            'medical_recommendations' => $this->medical_recommendations,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}