<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

final class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            [
                'name' => 'Donor ' . substr($request->phone, -4),
                'role' => 'donor',
                'active' => true,
                'email' => $request->phone . '@anasen.charity',
                'password' => Hash::make(str_random(16))
            ]
        );

        // MVP OTP: 12345
        $user->otp_code = '12345';
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        return response()->json([
            'message' => 'OTP sent successfully',
            'phone' => $request->phone
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
            return response()->json(['message' => 'Invalid or expired OTP'], 401);
        }

        // Clear OTP after verification if desired
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('anasen_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => $user->role,
                'avatar_url' => $user->getFileUrl('profile_photo_path'),
                'is_employee' => (bool) $user->is_employee,
                'is_volunteer' => (bool) $user->is_volunteer,
                'job_title' => $user->job_title,
            ]
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
