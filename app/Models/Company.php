<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
     use HasFactory;

     protected $fillable = [
        'name',
        'registration_number',
        'license_type',
        'expiry_date',
        'services_provided',
        'renewal_status',
    ];

    public function employees()
    {
        return $this->hasOne(EmployeePermit::class);
    }
}
