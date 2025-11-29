<?php

namespace App\Http\Controllers;

use App\Models\CommercialAddres;
use App\Models\Company;
use App\Models\User;
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

        // return $monthly;

        return Inertia::render('dashboard/crm/index', [
            'empolyes' => $empolyes,
            'companys' => $companys,
            'address' => $address,
            'monthly' => $monthly,
        ]);
    }
}
