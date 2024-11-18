<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'=> $this->id,
        'amount'=>$this->amount,
        'payment_method'=>$this->payment_method,
        'payment_status'=>$this->payment_status,
        'time'=>$this->time,
        'user_id'=>$this->user_id,   
        'patient_id'=>$this->patient_id,   
        'doctor_id'=>$this->doctor_id,   
        'nclinic_id'=>$this->nclinic_id  
        ];
        return parent::toArray($request);
    }
}
