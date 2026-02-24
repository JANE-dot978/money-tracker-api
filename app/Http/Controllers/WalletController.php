<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name'    => 'required|string|max:255',
        ]);

        $wallet = Wallet::create([
            'user_id' => $request->user_id,
            'name'    => $request->name,
            'balance' => 0.00,
        ]);

        return response()->json([
            'message' => 'Wallet created successfully',
            'wallet'  => $wallet,
        ], 201);
    }

    
    public function show($id)
    {
        $wallet = Wallet::with('transactions')->find($id);

        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found',
            ], 404);
        }

        return response()->json([
            'wallet'       => $wallet,
            'balance'      => $wallet->balance,
            'transactions' => $wallet->transactions,
        ], 200);
    }
}