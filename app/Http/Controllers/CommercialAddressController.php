<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CommercialAddres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class CommercialAddressController extends Controller
{
    public function commercialAddress(Request $request)
    {
        $address = CommercialAddres::all();

        $clients = Client::select('id', 'name')->get();

        $monthlyReports = CommercialAddres::select(
            DB::raw('SUM(net_profit) as total_profit')
        )
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->first();

        $quarterlyReports = CommercialAddres::whereBetween(
            'start_date',
            [
                now()->copy()->subMonths(3)->startOfMonth(),
                now()->endOfMonth(),
            ]
        )
            ->whereYear('start_date', now()->year)
            ->sum('net_profit');

        $yearlyReports = CommercialAddres::whereYear('start_date', now()->year)
            ->sum('net_profit');

        return Inertia::render('Commercial/Index', compact('address', 'clients', 'monthlyReports', 'quarterlyReports', 'yearlyReports'));
    }

    public function rangeReport(Request $request)
    {
        $request->validate([
            'start_month' => 'required|string',
            'end_month' => 'required|string',
        ]);

        // Convert YYYY-MM to full dates
        $startDate = Carbon::parse($request->start_month . '-01')->startOfMonth();
        $endDate = Carbon::parse($request->end_month . '-01')->endOfMonth();

        // Get total profit
        $totalProfit = CommercialAddres::whereBetween('start_date', [$startDate, $endDate])
            ->sum('net_profit');

        return response()->json([
            'total_profit' => $totalProfit
        ]);
    }

    public function commercialAddressStore(Request $request)
    {
        $request->validate([
            // 'name' => 'required|string|numeric|max:255',
            'contact_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_status' => 'required|string|max:255',
            'contact_value' => 'required|numeric',
            'business_center_cost' => 'required|numeric',
        ]);
        
        $netprofit = $request->contact_value - $request->business_center_cost;
        
        $clientId = $request->client_id;
        $clientData = Client::find($clientId);
        // return $request->all();

        CommercialAddres::create([
            'client_id' => $clientId,
            'name' => $clientData->name,
            'contact_type' => $request->contact_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_status' => $request->payment_status,
            'contact_value' => $request->contact_value,
            'business_center_cost' => $request->business_center_cost,
            'net_profit' => $netprofit,
        ]);

        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function commercialAddressUpdate(Request $request, $id)
    {
        $request->validate([
            'contact_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_status' => 'required|string|max:255',
            'contact_value' => 'required|numeric',
            'business_center_cost' => 'required|numeric',
        ]);

        $netprofit = $request->contact_value - $request->business_center_cost;

        CommercialAddres::where('id', $id)->update([
            'contact_type' => $request->contact_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_status' => $request->payment_status,
            'contact_value' => $request->contact_value,
            'business_center_cost' => $request->business_center_cost,
            'net_profit' => $netprofit,
            'status' => $request->status ?? 1,
        ]);

        return redirect()->back()->with('success', 'Address updated successfully.');
    }

    public function commercialAddressDestroy($id)
    {
        CommercialAddres::find($id)->delete();

        return redirect()->back()->with('success', 'Address deleted successfully.');
    }
}
