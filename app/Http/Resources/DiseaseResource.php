<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseResource extends JsonResource
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
        'disease_classification'=>$this->disease_classification,
        'disease_type'=>$this->disease_type,
        'description'=>$this->description,
        'major_id'=>$this->major_id,
        'patient_id'=>$this->patient_id,
        'doctor_id'=>$this->doctor_id
        ];
        return parent::toArray($request);
    }
}
