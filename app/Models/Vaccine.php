<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'doses'
    ];

    public function immunizations()
    {
        return $this->hasMany(Immunization::class);
    }
}
