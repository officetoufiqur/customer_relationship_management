<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateNumber;
use App\Models\Balance;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        $clients = Client::all();

        return Inertia::render('Invoice/Index', compact('invoices', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'service_name' => 'required',
            'service_description' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required',
            'tax_value' => 'required',
        ]);

        $invoiceNumber = GenerateNumber::generate('INV', Invoice::class);

        Invoice::create([
            'client_id' => $request->client_id,
            'invoice_number' => $invoiceNumber,
            'service_name' => $request->service_name,
            'service_description' => $request->service_description,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'tax_value' => $request->tax_value,
            'total' => $request->quantity * $request->unit_price,
        ]);

        return redirect()->back()->with('success', 'Invoice created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required',
            'service_name' => 'required',
            'service_description' => 'required',
            'quantity' => 'required',
            'unit_price' => 'required',
            'tax_value' => 'required',
        ]);

        $invoice = Invoice::find($id);
        $invoice->client_id = $request->client_id;
        $invoice->service_name = $request->service_name;
        $invoice->service_description = $request->service_description;
        $invoice->quantity = $request->quantity;
        $invoice->unit_price = $request->unit_price;
        $invoice->tax_value = $request->tax_value;
        $invoice->total = $request->quantity * $request->unit_price;
        $invoice->save();

        return redirect()->back()->with('success', 'Invoice updated successfully.');
    }

    public function download($id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);
        $pdf = PDF::loadView('invoices', compact('invoice'));

        return $pdf->download('invoice_'.$invoice->id.'.pdf');
    }

    public function payment(Request $request, $id)
    {
        $request->validate([
            'payment_type' => 'required',
            'payment_method' => 'required',
        ]);

        $method = $request->payment_method;

        $balance = Balance::where('type', $method)->first();
        $invoice = Invoice::find($id);

        if ($invoice->status == 'paid') {
            return back()->withErrors([
                'invoice' => 'Invoice already paid.',
            ]);
        }

        if ($request->payment_type == 'partial' && $request->partial_amount > $invoice->remaining_balance) {
            return back()->withErrors([
                'invoice' => 'Partial amount cannot be greater than remaining balance.',
            ]);
        }

        if ($request->payment_type == 'full' && $invoice->status == 'partial') {
            return back()->withErrors([
                'invoice' => 'Invoice is not fully paid It is in partial payment.',
            ]);
        }

        if ($request->payment_type == 'full') {
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'payment_method' => $method,
                'amount_paid' => $invoice->total,
                'remaining_balance' => 0,
            ]);

            $invoice->status = 'paid';
            $invoice->amount_paid = $invoice->total;
            $invoice->remaining_balance = 0;
            $invoice->save();

            $balance->current_balance = $balance->current_balance + $invoice->total;
            $balance->save();
        } else {
            $invoicePayment = InvoicePayment::where('invoice_id', $invoice->id)->first();

            if ($invoicePayment) {
                $invoicePayment->payment_method = $method;
                $invoicePayment->amount_paid += $request->partial_amount;
                $invoicePayment->remaining_balance = 0;
                $invoicePayment->save();

                $invoice->status = 'paid';
                $invoice->amount_paid += $request->partial_amount;
                $invoice->remaining_balance = 0;
                $invoice->save();

                $balance->current_balance = $balance->current_balance + $request->partial_amount;
                $balance->save();

            } else {
                InvoicePayment::create([
                    'invoice_id' => $invoice->id,
                    'payment_method' => $method,
                    'amount_paid' => $request->partial_amount,
                    'remaining_balance' => $invoice->total - $request->partial_amount,
                ]);
                
                $invoice->status = 'partial';
                $invoice->amount_paid = $request->partial_amount;
                $invoice->remaining_balance = $invoice->total - $request->partial_amount;
                $invoice->save();

                $balance->current_balance = $balance->current_balance + $request->partial_amount;
                $balance->save();

            }
        }

        return redirect()->back()->with('success', 'Invoice payment successfully.');
    }
}
