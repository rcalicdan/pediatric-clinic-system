<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'email',
        'relationship',
        'address'
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}
