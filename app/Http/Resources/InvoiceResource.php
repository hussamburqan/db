<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'reservation' => [
                'id' => $this->reservation->id,
                'date' => $this->reservation->date,
                'time' => $this->reservation->time,
                'doctor' => [
                    'id' => $this->reservation->doctor->id,
                    'name' => $this->reservation->doctor->user->name,
                    'specialization' => $this->reservation->doctor->specialization,
                ],
                'patient' => [
                    'id' => $this->reservation->patient->id,
                    'name' => $this->reservation->patient->user->name,
                    'phone' => $this->reservation->patient->user->phone,
                ],
            ],
        ];
    }
}