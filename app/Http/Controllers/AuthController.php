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

        $otp = $this->sendOtpViaBeon($user->phone);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // In production, send SMS here. For now, we log it.

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        $user = app(\App\Services\OtpService::class)->consume(User::class, $request->phone, $request->otp);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired OTP'
            ], 401);
        }


        $token = Token::issue($user);

        return response()->json([
            'status' => 'success',
            'token' => $token,
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
            'phone' => ['required', 'string', \Illuminate\Validation\Rule::unique('users', 'phone')->whereNull('deleted_at')],
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
        $otp = $this->sendOtpViaBeon($user->phone);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // In production, send SMS here.

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء الحساب بنجاح، يرجى تفعيل رقم الهاتف برمز التحقق المرسل إليك',
            'phone' => $user->phone
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'password' => 'required|string'
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب إدخال البريد الإلكتروني أو رقم الهاتف'
            ], 400);
        }

        if (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        } else {
            $user = User::where('phone', $data['phone'])->first();
        }

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        abort_unless($user->active, 401, 'Account is inactive');
        if ($user->registration_source === 'mobile' && !$user->phone_verified_at) {
            return response()->json(['message' => 'يرجى تفعيل رقم الهاتف أولاً', 'requires_otp' => true], 403);
        }
        $token = Token::issue($user);

        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $bearer = $request->bearerToken();
        if ($bearer) { Token::where('token', hash('sha256', $bearer))->where('hashed', true)->delete(); }
        return response()->noContent();
    }

    private function sendOtpViaBeon(string $phone): string
    {
        return app(\App\Services\OtpService::class)->send($phone);
    }
}
