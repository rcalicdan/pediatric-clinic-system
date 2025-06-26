<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Libraries\Audit\Auditable;
use App\Models\User;

class Guardian extends Model
{
    use HasFactory, Auditable;

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

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
