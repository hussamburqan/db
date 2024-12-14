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
            'description' => $this->description,
            'status' => $this->status,
            'instructions' => $this->instructions,
            'doctor' => $this->doctor ? [
                'id' => $this->doctor->id,
                'specialization' => $this->doctor->specialization,
                'education' => $this->doctor->education,
                'experience_years' => $this->doctor->experience_years,
                'photo' => $this->doctor->photo,
                'bio' => $this->doctor->bio,
                'user' => $this->doctor->user ? [
                    'id' => $this->doctor->user->id,
                    'name' => $this->doctor->user->name,
                    'email' => $this->doctor->user->email,
                    'phone' => $this->doctor->user->phone,
                ] : null,
            ] : null,
            'reservation' => $this->reservation ? [
                'id' => $this->reservation->id,
                'date' => $this->reservation->date,
                'time' => $this->reservation->time,
                'duration_minutes' => $this->reservation->duration_minutes,
                'status' => $this->reservation->status,
                'reason_for_visit' => $this->reservation->reason_for_visit,
                'notes' => $this->reservation->notes,
            ] : null,
            'patient' => [
                'id' => $this->reservation->patient->id,
                'user' => [
                    'id' => $this->reservation->patient->user->id,
                    'name' => $this->reservation->patient->user->name,
                    'email' => $this->reservation->patient->user->email,
                    'phone' => $this->reservation->patient->user->phone,
                ] ,
            ] ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
