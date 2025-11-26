<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'id_number',
        'position',
        'department',
        'employ_status',
        'salary',
        'allowances',
        'deductions',
        'annual_leave_balance',
        'sick_leave_balance',
        'location',
        'about',
        'skills',
        'socials',
        'image',
        'cover_photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function financialLogs()
    {
        return $this->hasMany(FinancialLog::class);
    }
}