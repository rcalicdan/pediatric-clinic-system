<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'total_amount',
        'paid',
        'payment_date',
        'payment_method'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
