<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'experience_years' => $this->experience_years,
            'specialization' => $this->specialization,
            'education' => $this->education,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'start_work_time' => $this->start_work_time,
            'end_work_time' => $this->end_work_time,
            'default_time_reservations' => $this->default_time_reservations,
            'bio' => $this->bio,
            
            'user' => [
                'id' => $this->whenLoaded('user', function() {
                    return $this->user->id;
                }),
                'name' => $this->whenLoaded('user', function() {
                    return $this->user->name;
                }),
                'email' => $this->whenLoaded('user', function() {
                    return $this->user->email;
                }),
                'phone' => $this->whenLoaded('user', function() {
                    return $this->user->phone;
                }),
                'address' => $this->whenLoaded('user', function() {
                    return $this->user->address;
                }),
            ],
            
            'major' => $this->whenLoaded('major', function() {
                return [
                    'id' => $this->major->id,
                    'name' => $this->major->name,
                ];
            }),
            
            'clinic' => $this->whenLoaded('clinic', function() {
                return [
                    'id' => $this->clinic->id,
                    'name' => $this->clinic->name,
                ];
            }),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}