<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class LoginWebController extends Controller
{
    public function show()
    {
        if (request()->session()->has('user_id') || request()->cookie('remember_user')) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string', 'remember' => 'nullable']);
        $email = trim($data['email']);

        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
        }
        $request->session()->put('user_id', $user->id);
        $response = redirect()->intended('/');
        if ($request->boolean('remember')) {
            $response->withCookie(cookie('remember_user', (string)$user->id, 60 * 24 * 30));
        }
        return $response;
    }
    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        cookie()->queue(cookie()->forget('remember_user'));
        return redirect()->route('login');
    }
}
