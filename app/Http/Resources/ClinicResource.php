<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClinicResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'major_id' => $this->major_id,
            'location' => $this->location,
            'photo' => $this->photo,
            'cover_photo' => $this->cover_photo,
            'description' => $this->description,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,

                ];
            }),
            
            'major' => $this->when($this->relationLoaded('major'), function () {
                return [
                    'id' => $this->major->id,
                    'name' => $this->major->name,
                ];
            }),
            
            'doctors_count' => $this->when($this->relationLoaded('doctors'), 
                function () {
                    return $this->doctors->count();
                }
            ),

            'doctors' => $this->when($this->relationLoaded('doctors'), function () {
                return $this->doctors->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'user' => [
                            'id' => $doctor->user->id,
                            'name' => $doctor->user->name,
                            'email' => $doctor->user->email,
                        ],
                        'photo' => $doctor->photo,
                        'specialization' => $doctor->specialization,
                        'experience_years' => $doctor->experience_years,
                        'education' => $doctor->education,
                        'start_work_time' => $doctor->start_work_time,
                        'end_work_time' => $doctor->end_work_time,
                        'default_time_reservations' => $doctor->default_time_reservations,
                        'bio' => $doctor->bio,

                    ];
                });
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}