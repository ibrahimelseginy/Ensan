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
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'exists' => true,
                'user' => [
                    'name' => $user->name,
                    'phone' => $user->phone
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'exists' => false,
            'message' => 'رقم الهاتف غير مسجل مسبقاً'
        ]);
    }

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
                'name' => $request->name ?? 'متبرع ' . substr($request->phone, -4),
                'email' => 'mobile_' . time() . '_' . rand(1000, 9999) . '@ensan.app',
                'password' => Hash::make(\Illuminate\Support\Str::random(16)),
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

        $user->otp_code = '123456'; // الثابت المطلوب للاختبار بتطبيق الموبايل
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
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6'
        ]);
        
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => 'mobile_' . time() . '_' . rand(1000, 9999) . '@ensan.app',
            'password' => Hash::make($data['password']),
            'active' => true,
            'registration_source' => 'mobile',
            'role' => 'donor'
        ]);

        // Assign donor role from Roles table
        $donorRole = \App\Models\Role::where('key', 'donor')->first();
        if ($donorRole) {
            $user->roles()->attach($donorRole->id);
        }

        // Set OTP for verification
        $user->otp_code = '123456'; // Default for testing
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // In production, send SMS here.
        \Illuminate\Support\Facades\Log::info("Register OTP for {$user->phone}: {$user->otp_code}");

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح، يرجى تفعيل رقم الهاتف برمز التحقق المرسل إليك',
            'phone' => $user->phone,
            'debug_otp' => config('app.debug') ? $user->otp_code : null
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('phone', $data['phone'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
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
