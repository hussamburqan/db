<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'medical_history' => $this->medical_history,
            'allergies' => $this->allergies,
            'blood_type' => $this->blood_type,
            'current_medications' => $this->current_medications,
            'medical_recommendations' => $this->medical_recommendations,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'address' => $this->user->address,
                'age' => $this->user->age,
                'gender' => $this->user->gender,
                'phone' => $this->user->phone,
            ],
            'diseases' => $this->diseases,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}