<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance_id',
        'created_by',
        'title',
        'description',
        'amount',
        'is_approved',
        'attachment',
    ];

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
    
}
