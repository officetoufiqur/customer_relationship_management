<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
     use HasFactory;

    protected $fillable = [
        'client_id',
        'invoice_number',
        'service_name',
        'service_description',
        'quantity',
        'unit_price',
        'tax_value',
        'total',
        'amount_paid',
        'remaining_balance',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}