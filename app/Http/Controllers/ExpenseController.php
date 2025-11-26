<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\FinancialLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function expenseList()
    {
        $expenses = Expense::all();
        $companys = Company::all();

        return Inertia::render('Expense/Index', [
            'expenses' => $expenses,
            'companys' => $companys,
        ]);
    }

    public function expenseRequest(Request $request)
    {
        $expenses = Expense::all();
        $companys = Company::all();

        $monthInput = $request->month;

        if ($monthInput) {
            $month = Carbon::parse($monthInput)->month;
            $year = Carbon::parse($monthInput)->year;
        } else {
            $month = now()->month;
            $year = now()->year;
        }

        $allExpenses = Expense::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('amount');

        $approvedExpenses = Expense::whereYear('created_at', $year)->whereMonth('created_at', $month)->where('status', 'approved')->count();
        $rejectedExpenses = Expense::whereYear('created_at', $year)->whereMonth('created_at', $month)->where('status', 'rejected')->count();

        $pendingExpenses = Expense::whereYear('created_at', $year)->whereMonth('created_at', $month)->where('status', 'pending')->count();

        $status = $request->status;

        if ($status == 'all') {
            $expenses = Expense::all();
        } elseif ($status == 'approved') {
            $expenses = Expense::where('status', 'approved')->get();
        } elseif ($status == 'rejected') {
            $expenses = Expense::where('status', 'rejected')->get();
        } elseif ($status == 'pending') {
            $expenses = Expense::where('status', 'pending')->get();
        }

        return Inertia::render('Expense/Request', [
            'expenses' => $expenses,
            'companys' => $companys,
            'allExpenses' => $allExpenses,
            'approvedExpenses' => $approvedExpenses,
            'rejectedExpenses' => $rejectedExpenses,
            'pendingExpenses' => $pendingExpenses,
        ]);
    }

    public function expenseStore(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'amount' => 'required|numeric',
            'recipient_name' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'client_paid' => 'required|numeric|max:255',
        ]);

        $company = Company::find($request->company_id);
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        $expense = new Expense;
        $expense->employee_id = $employee->id;
        $expense->company_id = $company->id;
        $expense->company_name = $company->name;
        $expense->amount = $request->amount;
        $expense->recipient_name = $request->recipient_name;
        $expense->payment_method = $request->payment_method;
        $expense->client_paid = $request->client_paid;
        $expense->reason = $request->reason;
        $expense->save();

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    public function expenseUpdate(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'recipient_name' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'client_paid' => 'required|numeric|max:255',
        ]);

        $expense = Expense::find($id);

        $expense->amount = $request->amount;
        $expense->recipient_name = $request->recipient_name;
        $expense->payment_method = $request->payment_method;
        $expense->client_paid = $request->client_paid;
        $expense->reason = $request->reason;
        $expense->save();

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function expenseDestroy($id)
    {
        Expense::find($id)->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function expenseStatus(Request $request, $id)
    {
        $expense = Expense::find($id);

        $expense->status = $request->status;
        $expense->save();

        if ($request->status == 'approved') {
            $financialLog = new FinancialLog;
            $financialLog->expense_id = $expense->id;
            $financialLog->employee_id = $expense->employee_id;
            $financialLog->company_id = $expense->company_id;
            $financialLog->amount = $expense->amount;
            $financialLog->save();
        }

        return redirect()->back()->with('success', 'Expense status updated successfully.');
    }

    public function financiaLogs()
    {
        $logs = FinancialLog::with('expense')->get();

        // return $logs;
        return Inertia::render('FinancialLog/Index', [
            'logs' => $logs
        ]);
    }
}
