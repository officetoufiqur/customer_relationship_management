<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
     use HasFactory;

     protected $fillable = [
        'name',
        'phone',
        'email',
        'whatsapp',
        'source_of_lead',
        'service_type',
        'follow_up_status',
        'client_interaction',
        'project_cost',
        'documents',
        'quotation_sent_status',
        'follow_date',
    ];


}
