<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReservationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'date' => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null,
            'time' => $this->time ? Carbon::parse($this->time)->format('H:i') : null,
            'duration_minutes' => $this->duration_minutes,
            'status' => $this->status,
            'reason_for_visit' => $this->reason_for_visit,
            'notes' => $this->notes,
            
            'patient' => $this->whenLoaded('patient', function() {
                return [
                    'id' => $this->patient->id,
                    'user' => [
                        'id' => $this->patient->user->id,
                        'name' => $this->patient->user->name,
                        'email' => $this->patient->user->email,
                        'phone' => $this->patient->user->phone ?? null,
                    ]
                ];
            }),
            
            'doctor' => $this->whenLoaded('doctor', function() {
                return [
                    'id' => $this->doctor->id,
                    'user' => [
                        'id' => $this->doctor->user->id,
                        'name' => $this->doctor->user->name,
                        'email' => $this->doctor->user->email,
                        'phone' => $this->doctor->user->phone ?? null,
                    ],
                    'speciality' => $this->doctor->speciality ?? null,
                ];
            }),
            
            'clinic' => $this->whenLoaded('clinic', function() {
                return [
                    'id' => $this->clinic->id,
                    'name' => $this->clinic->name,
                ];
            }),
            
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}