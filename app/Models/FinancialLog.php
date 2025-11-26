<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
     use HasFactory;

    protected $fillable = [
        'expense_id',
        'employee_id',
        'company_id',
        'amount',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
