<?php

namespace App\Http\Controllers\Api\Anasen;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Request OTP for a phone number
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // For MVP, we use a fixed OTP 12345
            $otp = '12345';
            $normalizedName = trim((string) $request->input('name', ''));
            $normalizedName = $normalizedName !== '' ? $normalizedName : null;
            
            // Try to find an existing legacy Donor record to get the real name
            $legacyDonor = \App\Models\Donor::where('phone', $request->phone)->first();
            $defaultName = $normalizedName
                ?: ($legacyDonor ? $legacyDonor->name : ('Donor ' . substr($request->phone, -4)));

            $user = \App\Models\WebDonor::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'name' => $defaultName,
                    'email' => $request->phone . '@anasen.tmp',
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'active' => true,
                ]
            );

            if ($normalizedName && $user->name !== $normalizedName) {
                $user->name = $normalizedName;
            }

            $user->otp_code = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            Log::info('Web Donor OTP requested', ['phone' => $request->phone]);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully (MVP: 12345)',
            ]);

        } catch (\Exception $e) {
            Log::error('Web Donor OTP login failed', [
                'error' => $e->getMessage(),
                'phone' => $request->phone
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process login request',
            ], 500);
        }
    }

    /**
     * POST /api/auth/verify-otp
     * Verify OTP and return custom API token
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $normalizedName = trim((string) $request->input('name', ''));
            $normalizedName = $normalizedName !== '' ? $normalizedName : null;

            $user = \App\Models\WebDonor::where('phone', $request->phone)
                        ->where('otp_code', $request->otp)
                        ->where('otp_expires_at', '>', Carbon::now())
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP',
                ], 401);
            }

            // Clear OTP after successful verification
            if ($normalizedName && $user->name !== $normalizedName) {
                $user->name = $normalizedName;
            }
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            $tokenResult = $user->createToken('anasen_api_token');
            $token = $tokenResult->plainTextToken;

            Log::info('Web Donor verified OTP', ['user_id' => $user->id, 'phone' => $user->phone]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'role' => 'donor', // Kept for frontend compatibility
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'phone' => $request->phone
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
            ], 500);
        }
    }

    /**
     * POST /api/auth/logout
     * Revoke current access token
     */
    public function logout(Request $request)
    {
        try {
            $header = $request->header('Authorization');
            $tokenStr = str_replace('Bearer ', '', $header);
            
            PersonalAccessToken::where('token', hash('sha256', $tokenStr))->delete();

            Log::info('User logged out', ['user_id' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
            ], 500);
        }
    }
}
