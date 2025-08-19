<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Suppost\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Carbon;
use App\Mail\VerifyOtpMail;

class AuthController extends Controller
{
    public function register(Request $request){
        $validated = $request -> validate([
            'username' => ['required', 'string', 'max:16', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'id_number' => ['nullable', 'string', 'max:15'],
            'prodi' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'id_number' => $validated['id_number'] ?? null,
            'prodi' => $validated['prodi'],
        ]);

        // Fire event (still compatible with link-based verification)
        event(new Registered($user));

        // Generate OTP and send
        $this->generateAndSendOtp($user);

        // $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan cek email untuk verifikasi.',
            'email' => $user->email
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:16'],
            'password' => ['required', 'min:8'],
        ]);

        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // $user = User::where('username', $credentials['username'])->first();
        $user = Auth::user(); // Get the authenticated user directly

         // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Verifikasi email Anda terlebih dahulu.'
            ], 403);
        }

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
        return response()->json(['message' => 'Berhasil logout']);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $this->generateAndSendOtp($user);

        return response()->json(['message' => 'OTP berhasil dikirim']);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            $user->email_verification_code !== $request->otp ||
            Carbon::parse($user->email_verification_expires_at)->isPast()
        ) {
            return response()->json(['message' => 'OTP tidak valid atau telah kedaluwarsa'], 400);
        }

        $user->email_verification_code = null;
        $user->email_verification_expires_at = null;
        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully']);
    }

    // (Optional) Support existing verification link
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully']);
    }

    // Helper: generate OTP and send email
    private function generateAndSendOtp(User $user)
    {
        $otp = rand(100000, 999999);

        $user->email_verification_code = $otp;
        $user->email_verification_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new VerifyOtpMail($otp));
    }


    
}

