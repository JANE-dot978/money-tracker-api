<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $user = User::create([
    'name'     => $request->name,
    'email'    => $request->email,
    'password' => bcrypt('password123'),
  ]);
        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user,
        ], 201);
    }

    // Get user profile with all wallets and total balance
    public function show($id)
    {
        $user = User::with('wallets')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user'          => $user,
            'total_balance' => $user->totalBalance(),
        ], 200);
    }
}