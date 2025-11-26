<?php

namespace App\Http\Controllers;

use App\Models\FinancialLog;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $invoice = FinancialLog::with('expense')->findOrFail($id);

        $pdf = PDF::loadView('invoices', compact('invoice'));

        return $pdf->download('invoice_'.$invoice->id.'.pdf');
    }
}
