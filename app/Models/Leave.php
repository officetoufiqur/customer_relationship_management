<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
   use HasFactory;

    protected $fillable = [
        'employe_id',
        'leave_type',
        'reason',
        'status',
        'start_date',
        'end_date',
        'is_medical',
        'medical_excuse_file'
    ];

}
