<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'age'=>$this->age,
            'email'=>$this->email,
            'gender'=>$this->gender,
            'phone'=>$this->phone,
            'specialization'=>$this->specialization,
            'experience'=>$this->experience,
            'education'=>$this->education,
            'major_id'=>$this->major_id,
            'nclinic_id'=>$this->nclinic_id,
            'user_id'=>$this->user_id,

            //////////////////////////////////
            //////////////////////////////////

            'appointments' => AppointmentResource::collection( $this->appointments)->count(),
            'appointments' => $this->appointmentsCount(),

            ///////////////////////////


            'medications' => MedicationResource::collection( $this->medications)->count(),
            'medications' => $this->medicationsCount(),

            //////////////////////////

            'prescriptions' => PrescriptionResource::collection( $this->prescriptions)->count(),
            'prescriptions' => $this->prescriptionsCount(),

            //////////////////////////

            'reservations' => ReservationResource::collection( $this->reservations)->count(),
            'reservations' => $this->reservationsCount(),

            //////////////////////////

            'patients' => PatientResource::collection( $this->patients)->count(),
            'patients' => $this->patientsCount(),
        ];
        return parent::toArray($request);
    }
}

/*
        'name', 
        'age',
        'email',
        'gender',
        'phone',
        'specialization',
        'experience',
        'education',
        'major_id',
        'nclinic_id',
        'user_id'
*/
