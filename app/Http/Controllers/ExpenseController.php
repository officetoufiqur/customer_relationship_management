<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function expenseList()
    {
        $expenses = Expense::all();

        $companys = Company::all();

        return Inertia::render('Expense/Index', compact('expenses', 'companys'));
    }
}
