<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json(data: Auth::user());
            //return response()->json(['message' => 'Login successful!']);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    public function user(Request $request)
    {
        // return $request->user();
        return response()->json(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logout successful!']);
    }
}

