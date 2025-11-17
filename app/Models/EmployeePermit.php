<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePermit extends Model
{
    use HasFactory;

     protected $fillable = [
        'company_id',
        'employee_name',
        'passport_number',
        'permit_expiry',
        'permit_type',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
