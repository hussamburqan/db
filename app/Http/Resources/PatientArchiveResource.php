<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientArchiveResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'description' => $this->description,
            'status' => $this->status,
            'instructions' => $this->instructions,
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user->name,
                'specialization' => $this->doctor->specialization,
                'education' => $this->doctor->education,
                'experience_years' => $this->doctor->experience_years,
                'photo' => $this->doctor->photo,
                'bio' => $this->doctor->bio
            ],
            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}