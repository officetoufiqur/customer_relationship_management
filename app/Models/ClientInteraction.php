<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientInteraction extends Model
{
    use HasFactory;

     protected $fillable = [
        'client_id',
        'client_interaction_type',
        'documents',
        'quotation_sent_status',
        'follow_up_date',
        'notes',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
