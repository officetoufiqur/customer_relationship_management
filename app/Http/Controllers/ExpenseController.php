<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Balance;
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
    public function index()
    {
        $expenses = Expense::all();
        $balances = Balance::select('id', 'type', 'name')->get();

        return Inertia::render('Expense/Index', [
            'expenses' => $expenses,
            'balances' => $balances,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'balance_id' => 'required',
            'title' => 'required',
            'amount' => 'required|numeric',
            'description' => 'required',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = FileUpload::storeFile($request->file('attachment'), 'uploads/expense');

        Expense::create([
            'balance_id' => $request->balance_id,
            'created_by' => Auth::user()->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
            'attachment' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Expense request submitted successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'balance_id' => 'required',
            'title' => 'required',
            'amount' => 'required|numeric',
            'description' => 'required',
            // 'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        // return $request->all();

        $expense = Expense::find($id);

        if ($request->hasFile('attachment')) {
            $filePath = FileUpload::updateFile($request->file('attachment'), 'uploads/expense', $expense->attachment);
            $expense->attachment = $filePath;
        }

        $expense->balance_id = $request->balance_id;
        $expense->title = $request->title;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->save();

        return redirect()->back()->with('success', 'Expense request updated successfully.');
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
        $expense = Expense::find($id);

        if ($expense->attachment) {
            FileUpload::deleteFile($expense->attachment);
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function expenseStatus(Request $request, $id)
    {
        $expense = Expense::find($id);

        $expense->is_approved = $request->is_approved;
        $expense->save();

        if ($request->is_approved == '1') {
            $balance = Balance::where('id', $expense->balance_id)->first();

            $balance->current_balance = $balance->current_balance - $expense->amount;
            $balance->save();
        }

        return redirect()->back()->with('success', 'Expense status updated successfully.');
    }

    public function financiaLogs()
    {
        $logs = FinancialLog::with('expense')->get();

        // return $logs;
        return Inertia::render('FinancialLog/Index', [
            'logs' => $logs,
        ]);
    }
}
