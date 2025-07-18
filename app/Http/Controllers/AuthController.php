<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Suppost\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::validate($credentials)) {
            //return response()->json(data: Auth::user());
            return response()->json(['message' => 'Invalid Creds!']);
        }

        $user = User::where('username', $credentials['username'])->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function user(Request $request)
    {
        // // return $request->user();
        // return response()->json(Auth::user());
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // Auth::logout();
        // // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        return response()->json(['message' => 'Logout successful!']);
    }
}

