<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

final class AuthController extends Controller
{
    public function loginByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'nullable|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $user = User::create([
                'phone' => $request->phone,
                'name' => $request->name ?? 'Donor ' . substr($request->phone, -4),
                'role' => 'donor',
                'active' => true,
                'registration_source' => 'mobile'
            ]);

            // Assign donor role from Roles table if exists
            $donorRole = \App\Models\Role::where('key', 'donor')->first();
            if ($donorRole) {
                $user->roles()->attach($donorRole->id);
            }
        }

        $user->otp_code = (string) random_int(100000, 999999);
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // In production, send SMS here. For now, we log it.
        \Illuminate\Support\Facades\Log::info("OTP for {$request->phone}: {$user->otp_code}");

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'debug_otp' => config('app.debug') ? $user->otp_code : null
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('otp_code', $request->otp)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP'
            ], 401);
        }

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = Token::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token->token,
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone
            ]
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:users,phone'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'active' => true,
            'registration_source' => 'mobile',
            'role' => 'donor'
        ]);

        // Assign donor role from Roles table
        $donorRole = \App\Models\Role::where('key', 'donor')->first();
        if ($donorRole) {
            $user->roles()->attach($donorRole->id);
        }

        $token = Token::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token->token,
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone
            ]
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = Token::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
        ]);
        return response()->json(['token' => $token->token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $bearer = $request->bearerToken();
        if ($bearer) { Token::where('token', $bearer)->delete(); }
        return response()->noContent();
    }
}
