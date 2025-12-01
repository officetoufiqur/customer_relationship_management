<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationController extends Controller
{
    public function index()
    {
        $quotation = Quotation::all();

        $clients = Client::all();

        return Inertia::render('Quotation/Index', compact('quotation', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'service_name' => 'required',
            'service_description' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required',
            'tax' => 'required',
        ]);

        // Q-2025-001
        $year = date('Y');
        $lastQuotation = Quotation::whereYear('created_at', $year)
            ->latest('id')
            ->first();

        $lastNumber = 0;
        if ($lastQuotation) {
            $lastNumber = intval(substr($lastQuotation->quotation_number, -3));
        }

        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $quotation_number = 'Q-'.$year.'-'.$newNumber;

        Quotation::create([
            'client_id' => $request->client_id,
            'quotation_number' => $quotation_number,
            'service_name' => $request->service_name,
            'service_description' => $request->service_description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'tax' => $request->tax,
            'total_amount' => $request->quantity * $request->unit_price,
        ]);

        return redirect()->back()->with('success', 'Quotation created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required',
            'service_name' => 'required',
            'service_description' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required',
            'tax' => 'required',
        ]);

        $quotation = Quotation::find($id);
        $quotation->client_id = $request->client_id;
        $quotation->service_name = $request->service_name;
        $quotation->service_description = $request->service_description;
        $quotation->quantity = $request->quantity;
        $quotation->unit_price = $request->unit_price;
        $quotation->tax = $request->tax;
        $quotation->total_amount = $request->quantity * $request->unit_price;
        $quotation->save();

        return redirect()->back()->with('success', 'Quotation updated successfully.');
    }

    public function destroy($id)
    {
        Quotation::find($id)->delete();
        return redirect()->back()->with('success', 'Quotation deleted successfully.');
    }
}
