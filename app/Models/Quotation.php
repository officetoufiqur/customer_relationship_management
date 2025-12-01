<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'quotation_number',
        'service_name',
        'service_description',
        'quantity',
        'unit_price',
        'tax',
        'total_amount',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}