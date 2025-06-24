<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'guardian_id'
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class);
    }
}
