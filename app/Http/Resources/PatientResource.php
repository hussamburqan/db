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
            'archives' => $this->archives->map(function($archive) {
                return [
                    'id' => $archive->id,
                    'date' => $archive->date,
                    'time' => $archive->time,
                    'description' => $archive->description,
                    'status' => $archive->status,
                    'instructions' => $archive->instructions,
                    'doctor' => [
                        'id' => $archive->doctor->id,
                        'name' => $archive->doctor->user->name,
                        'specialization' => $archive->doctor->specialization,
                        'photo' => $archive->doctor->photo,
                        'work_time' => [
                            'start' => $archive->doctor->start_work_time,
                            'end' => $archive->doctor->end_work_time
                        ]
                    ],
                    'clinic' => [
                        'id' => $archive->doctor->clinic->id,
                        'location' => $archive->doctor->clinic->location,
                        'photo' => $archive->doctor->clinic->photo,
                        'opening_time' => $archive->doctor->clinic->opening_time,
                        'closing_time' => $archive->doctor->clinic->closing_time
                    ]
                ];
            }),
            'diseases' => $this->diseases,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}