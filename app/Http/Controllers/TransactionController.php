<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Add a transaction to a wallet
    public function store(Request $request)
    {
        $request->validate([
            'wallet_id'   => 'required|exists:wallets,id',
            'amount'      => 'required|numeric|min:0.01',
            'type'        => 'required|in:income,expense',
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::find($request->wallet_id);

        // Check if expense exceeds balance
        if ($request->type === 'expense' && $request->amount > $wallet->balance) {
            return response()->json([
                'message' => 'Insufficient balance for this expense',
            ], 422);
        }

        // Create the transaction
        $transaction = Transaction::create([
            'wallet_id'   => $request->wallet_id,
            'amount'      => $request->amount,
            'type'        => $request->type,
            'description' => $request->description,
        ]);

        // Update wallet balance
        if ($request->type === 'income') {
            $wallet->balance += $request->amount;
        } else {
            $wallet->balance -= $request->amount;
        }
        $wallet->save();

        return response()->json([
            'message'     => 'Transaction added successfully',
            'transaction' => $transaction,
            'new_balance' => $wallet->balance,
        ], 201);
    }
}