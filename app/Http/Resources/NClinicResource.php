<?php
// app/Http/Resources/NClinicResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NClinicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'email' => $this->email,
            'phone' => $this->phone,
            'major' => new MajorResource($this->whenLoaded('major')),
            'user' => new UserResource($this->whenLoaded('user')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'created_at' => $this->created_at
        ];
    }
}