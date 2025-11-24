<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialAddres extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_type',
        'start_date',
        'end_date',
        'payment_status',
        'status',
        'contact_value',
        'business_center_cost',
        'net_profit',
    ];


}
