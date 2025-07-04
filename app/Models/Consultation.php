<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Audit\Auditable;
use App\Models\User;

class Consultation extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'appointment_id',
        'user_id',
        'diagnosis',
        'treatment',
        'prescription',
        'height_cm',
        'weight_kg',
        'temperature_c',
        'notes'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
