<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CommercialAddres;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommercialAddressController extends Controller
{
    public function commercialAddress()
    {
        $address = CommercialAddres::all();

        $clients = Client::select('id', 'name')->get();

        return Inertia::render('Commercial/Index', compact('address', 'clients'));
    }

    public function commercialAddressStore(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_status' => 'required|string|max:255',
            'contact_value' => 'required|string|max:255',
            'business_center_cost' => 'required|string|max:255',
        ]);  
        
        $netprofit = $request->contact_value - $request->business_center_cost;

        CommercialAddres::create([
            'name' => $request->name,
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
}
