<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
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
        'project_cost',
    ];

    public function clientInteractions()
    {
        return $this->hasOne(ClientInteraction::class);
    }
}
