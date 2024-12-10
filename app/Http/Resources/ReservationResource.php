<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ReservationResource extends JsonResource {     
    public function toArray($request)     
    {         
        return [             
            'id' => $this->id,             
            'date' => $this->date ? Carbon::parse($this->date)->format('Y-m-d') : '',             
            'time' => $this->time ? Carbon::parse($this->time)->format('H:i') : '',             
            'duration_minutes' => $this->duration_minutes ?? 0,             
            'status' => $this->status ?? '',             
            'reason_for_visit' => $this->reason_for_visit ?? '',             
            'notes' => $this->notes,             
            'doctor' => [                     
                'id' => $this->doctor->id,                     
                'user' => [                         
                    'id' => $this->doctor->user->id,                         
                    'name' => $this->doctor->user->name,                      
                ],                     
                'photo' => $this->doctor->photo ?? '',                     
                'speciality' => $this->doctor->specialization ?? '',                 
            ],             
            'clinic' => [                     
                'id' => $this->clinic->id,
                'user_id' => $this->clinic->user_id,
                'major_id' => $this->clinic->major_id,
                'location' => $this->clinic->location ?? '',
                'photo' => $this->clinic->photo,
                'cover_photo' => $this->clinic->cover_photo,
                'description' => $this->clinic->description ?? '',
                'opening_time' => $this->clinic->opening_time ?? '',
                'closing_time' => $this->clinic->closing_time ?? '',
                'user' => [
                    'id' => $this->clinic->user->id,
                    'name' => $this->patient->user->name,
                    'phone' => $this->patient->user->phone,
                    'age' => $this->patient->user->age,
                    'gender' => $this->patient->user->gender,
                ],
                'major' => [
                    'id' => $this->clinic->major->id,
                    'name' => $this->clinic->major->name,
                ],
            ],
            'patient' => [
                'id' => $this->patient->id,
                'user' => [
                    'name' => $this->patient->user->name,
                    'phone' => $this->patient->user->phone,
                    'age' => $this->patient->user->age,
                    'gender' => $this->patient->user->gender,
                ],
            ],
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,             
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,         
        ];     
    } 
}