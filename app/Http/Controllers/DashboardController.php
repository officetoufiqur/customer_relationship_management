<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Client;
use App\Models\CommercialAddres;
use App\Models\Company;
use App\Models\Expense;
use App\Models\InvoicePayment;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $empolyes = User::with('employee')->where('id', '!=', 1)->count();
        $companys = Company::count();
        $address = CommercialAddres::count();
        $totalAmount = Balance::sum('current_balance');
        // return $totalAmount;

        $dealPending = Client::where('follow_up_status', 'pending')->count();
        $dealWon = Client::where('follow_up_status', 'approved')->count();
        $dealLoss = Client::where('follow_up_status', 'lost')->count();

        $performanceData = TaskUser::select(
            'assigned_to',
            DB::raw("ROUND(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as overall_percent")
        )
            ->with('user:id,name,email')
            ->groupBy('assigned_to')
            ->get();

        $performance = $performanceData->map(function ($item) {
            return [
                'id' => $item->assigned_to,
                'name' => $item->user->name,
                'email' => $item->user->email,
                'performance' => $item->overall_percent,
            ];
        });

        $tasks = Task::all();

        // Get revinue and expense
        $revinue = InvoicePayment::sum('amount_paid');
        $expense = Expense::sum('amount');

        // get monthly revinue and expense
        $monthRevinue = InvoicePayment::selectRaw('MONTH(created_at) as month, SUM(amount_paid) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $expenses = Expense::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        // 12 months initialize with 0
        $monthWise = collect(range(1, 12))->map(function ($month) use ($expenses) {
            return $expenses[$month] ?? 0;
        });

        $montlyRevinue = collect(range(1, 12))->map(function ($month) use ($monthRevinue) {
            return $monthRevinue[$month] ?? 0;
        });

        // return $montlyRevinue;

        return Inertia::render('dashboard/crm/index', [
            'empolyes' => $empolyes,
            'companys' => $companys,
            'address' => $address,
            'dealPending' => $dealPending,
            'dealWon' => $dealWon,
            'dealLoss' => $dealLoss,
            'performance' => $performance,
            'tasks' => $tasks,
            'revinue' => $revinue,
            'expense' => $expense,
            'monthWise' => $monthWise,
            'montlyRevinue' => $montlyRevinue,
            'totalAmount' => $totalAmount
        ]);
    }
}
