<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CommercialAddres;
use App\Models\Company;
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

        // Monthly Revenue & Expenses for chart
        $rawMonthlyData = CommercialAddres::selectRaw('
        MONTH(created_at) AS month,
        SUM(net_profit) AS revenue,
        SUM(contact_value) AS expenses
    ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Build 12 months array
        $monthly = collect(range(1, 12))->map(function ($m) use ($rawMonthlyData) {
            return [
                'month' => $m,
                'revenue' => isset($rawMonthlyData[$m]) ? (float) $rawMonthlyData[$m]->revenue : 0,
                'expenses' => isset($rawMonthlyData[$m]) ? (float) $rawMonthlyData[$m]->expenses : 0,
            ];
        });

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

        return Inertia::render('dashboard/crm/index', [
            'empolyes' => $empolyes,
            'companys' => $companys,
            'address' => $address,
            'monthly' => $monthly,
            'dealPending' => $dealPending,
            'dealWon' => $dealWon,
            'dealLoss' => $dealLoss,
            'performance' => $performance,
            'tasks' => $tasks
        ]);
    }
}
