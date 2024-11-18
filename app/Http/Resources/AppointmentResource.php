<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointsmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
        'id'=>$this->id,    
        'date'=>$this->date,
        'time'=>$this->time,
        'description'=>$this->description,
        'status'=>$this->status,
        'patient_id'=>$this->patient_id,
        'nclinic_id'=>$this->nclinic_id,
        'doctor_id'=>$this->doctor_id,
        'user_id'=>$this->user_id
        ];
        return parent::toArray($request);
    }
}
