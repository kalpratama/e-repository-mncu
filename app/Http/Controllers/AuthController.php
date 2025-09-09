<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Carbon;
use App\Mail\VerifyOtpMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        Log::info(' ');
        Log::info('[STEP 1] Incoming registration request.', [
            'payload' => $request->except('password') // hide password for security
        ]);

        try {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:16', 'unique:users'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'id_number' => ['nullable', 'string', 'max:15'],
                'prodi' => ['required', 'string', 'max:255'],
            ]);

            Log::info('[STEP 2] Validation successful.', [
                'validated_data' => $validated
            ]);

            \DB::beginTransaction();
            Log::info('[STEP 3] Database transaction started.');

            $user = User::create([
                'username' => $validated['username'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'id_number' => $validated['id_number'] ?? null,
                'prodi' => $validated['prodi'],
            ]);

            Log::info('[STEP 4] User inserted into DB successfully.', ['user_id' => $user->id]);

            // Fire Laravel's default Registered event
            event(new Registered($user));
            Log::info('[STEP 5] Registered event fired for user.', ['user_id' => $user->id]);

            try {
                $this->generateAndSendOtp($user);
                Log::info('[STEP 6] OTP sent successfully.', ['email' => $user->email]);
            } catch (\Exception $e) {
                \DB::rollBack();
                Log::error('[STEP 6-FAIL] Failed to send OTP. Rolling back user registration.', [
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'message' => 'Gagal mengirim OTP. Registrasi dibatalkan.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            \DB::commit();
            Log::info('[STEP 7] Registration transaction committed successfully.', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'message' => 'Registrasi berhasil. Silakan cek email untuk verifikasi.',
                'email' => $user->email
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[VALIDATION FAIL] Registration request rejected.', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('[REGISTER FAIL] Unexpected error occurred during registration.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
            ], 500);
        }
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

        $user = Auth::user(); // Get the authenticated user directly

         // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            // If account is older than 10 minutes and still unverified, delete it
            if ($user->created_at->diffInMinutes(now()) >= 1) {
                $user->delete();
                $this->cleanupUnverified();
                return response()->json([
                    'message' => 'Akun Anda dihapus karena tidak memverifikasi email dalam 10 menit. Silakan registrasi ulang.'
                ], 403);
            }

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
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
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

    private function generateAndSendOtp(User $user)
    {
        try {
            Log::info('[OTP-STEP 1] Starting OTP generation.', ['user_id' => $user->id]);

            $otp = rand(100000, 999999);

            $user->email_verification_code = $otp;
            $user->email_verification_expires_at = now()->addMinutes(10);
            $user->save();
            Log::info('[OTP-STEP 2] OTP saved to DB.', [
                'user_id' => $user->id,
                'otp' => $otp
            ]);

            Mail::to($user->email)->send(new VerifyOtpMail($otp));
            Log::info('[OTP-STEP 3] OTP email sent successfully.', ['email' => $user->email]);

        } catch (\Exception $e) {
            Log::error('[OTP-FAIL] Failed to send OTP.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function cleanupUnverified()
    {
        $deleted = User::whereNull('email_verified_at')
            ->where('created_at', '<', now()->subMinutes(1))
            ->delete();

        return response()->json([
            'message' => "$deleted akun yang belum diverifikasi telah dihapus."
        ]);
    }

    public function debugSendOTP(Request $request)
    {
        try {
            $email = $request->input('email');

            if (!$email) {
                return response()->json(['message' => 'Email is required'], 400);
            }

            // Generate random OTP (6 digits)
            $otp = rand(100000, 999999);

            // Send email using existing VerifyOtpMail Mailable
            Mail::to($email)->send(new VerifyOtpMail($otp));

            return response()->json([
                'message' => 'Debug OTP sent successfully',
                'otp' => $otp
            ], 200);
        } catch (\Exception $e) {
            \Log::error("DEBUG OTP SEND ERROR: " . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

