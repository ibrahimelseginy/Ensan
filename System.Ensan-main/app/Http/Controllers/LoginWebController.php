<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class LoginWebController extends Controller
{
    public function show()
    {
        if (Auth::check() || request()->session()->has('user_id') || request()->cookie('remember_user')) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string', 'remember' => 'nullable']);
        $email = trim($data['email']);

        $user = User::with('roles.permissions')->where('email', $email)->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
        }

        if (!$user->active) {
            return back()->withErrors(['email' => 'هذا الحساب غير نشط، يرجى مراجعة مسؤول النظام']);
        }

        if ($user->roles->isEmpty()) {
            return back()->withErrors(['email' => 'هذا الحساب غير مرتبط بدور أو صلاحيات للدخول']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        if ($user->hasRole('admin')) {
            $request->session()->flash('show_admin_notifications', true);
        }
        $response = redirect()->intended(route('dashboard.index'));
        if ($request->boolean('remember')) {
            $response->withCookie(cookie('remember_user', (string)$user->id, 60 * 24 * 30));
        }
        return $response;
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        cookie()->queue(cookie()->forget('remember_user'));
        return redirect()->route('login');
    }
}
