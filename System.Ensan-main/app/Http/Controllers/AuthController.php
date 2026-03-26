<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class AuthController extends Controller
{
    public function loginByPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string']);

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

        $user->otp_code = '12345'; // MVP OTP
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        return response()->json(['message' => 'OTP sent successfully']);
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

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = Token::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
        ]);

        return response()->json(['token' => $token->token, 'user' => $user]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'active' => true
        ]);
        $token = Token::create([
            'user_id' => $user->id,
            'token' => bin2hex(random_bytes(32)),
        ]);
        return response()->json(['token' => $token->token, 'user' => $user]);
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
