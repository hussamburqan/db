<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 
        'amount', 
        'payment_method', 
        'payment_status',
        'paid_at', 
        'notes', 
        'reservation_id', 
        'nclinic_id'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function clinic()
    {
        return $this->belongsTo(NClinic::class, 'nclinic_id');
    }
}