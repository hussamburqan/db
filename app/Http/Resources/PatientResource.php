<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
   public function toArray($request)
   {
       return [
           'id' => $this->id,
           'emergency_contact' => $this->emergency_contact,
           'emergency_phone' => $this->emergency_phone,
           'medical_history' => $this->medical_history,
           'allergies' => $this->allergies,
           'current_medications' => $this->current_medications,
           'medical_recommendations' => $this->medical_recommendations,
           'user_id' => $this->user_id,
           'created_at' => $this->created_at,
           'updated_at' => $this->updated_at,
           'user' => [
               'id' => $this->user->id,
               'name' => $this->user->name,
               'email' => $this->user->email,
               'address' => $this->user->address,
               'age' => $this->user->age,
               'blood_type' => $this->user->blood_type,
               'gender' => $this->user->gender,
               'phone' => $this->user->phone,
               'created_at' => $this->user->created_at,
               'updated_at' => $this->user->updated_at,
           ],
           'appointments' => $this->appointments->map(function($appointment) {
               return [
                   'id' => $appointment->id,
                   'date' => $appointment->date,
                   'time' => $appointment->time,
                   'description' => $appointment->description,
                   'status' => $appointment->status,
                   'instructions' => $appointment->instructions,
                   'patient_id' => $appointment->patient_id,
                   'doctor' => [
                       'id' => $appointment->doctor->id,
                       'name' => $appointment->doctor->name,
                       'specialization' => $appointment->doctor->specialization,
                       'education' => $appointment->doctor->education,
                       'experience_years' => $appointment->doctor->experience_years,
                       'photo' => $appointment->doctor->photo,
                       'bio' => $appointment->doctor->bio
                   ],
                   'clinic' => [
                       'id' => $appointment->nclinic->id,
                       'name' => $appointment->nclinic->name,
                       'location' => $appointment->nclinic->location,
                       'opening_time' => $appointment->nclinic->opening_time,
                       'closing_time' => $appointment->nclinic->closing_time,
                       'status' => $appointment->nclinic->status,
                       'email' => $appointment->nclinic->email,
                       'phone' => $appointment->nclinic->phone,
                       'photo' => $appointment->nclinic->photo
                   ],
                   'created_at' => $appointment->created_at,
                   'updated_at' => $appointment->updated_at,
               ];
           }),
       ];
   }
}