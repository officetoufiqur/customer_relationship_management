<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BalanceController extends Controller
{
    public function index()
    {
        $balance = Balance::all();

        return Inertia::render('Balance/Index', compact('balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bank,credit_card,cash',
            'opening_balance' => 'required|numeric',
        ]);

        $balance = Balance::select('type')->get();

        foreach ($balance as $bal) {
            if ($bal->type == $request->type) {
                return response()->json(['error' => 'Balance already initialized'], 400);
            }
        }

        // Map type → name
        $names = [
            'bank' => 'Bank',
            'credit_card' => 'Credit Card',
            'cash' => 'Cash name',
        ];

        Balance::create([
            'type' => $request->type,
            'name' => $names[$request->type],
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
        ]);

        return redirect()->back()->with('success', 'Balance created successfully.');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'transfer_amount' => 'required|numeric',
        ]);

        $bank = Balance::where('type', 'bank')->first();
        $credit = Balance::where('type', 'credit_card')->first();
        $amount = $request->transfer_amount;

        if ($bank->current_balance < $amount) {
            return back()->withErrors([
                'transfer_amount' => 'Insufficient balance.',
            ]);
        }

        $bank->current_balance -= $amount;
        $credit->current_balance += $amount;

        $bank->save();
        $credit->save();

        return back()->with('message', 'Transfer successful.');
    }
}
