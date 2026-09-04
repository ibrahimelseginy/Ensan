<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $id = $user?->id ?? $request->session()->get('user_id');

        if (!$id) {
            $remember = $request->cookie('remember_user');
            if ($remember) {
                $request->session()->put('user_id', (int)$remember);
                $id = (int)$remember;
            }
        }
        if (!$id) {
            return redirect()->route('login');
        }

        $user = User::with('roles.permissions')->find($id);
        if (!$user || !$user->active || $user->roles->isEmpty()) {
            $request->session()->forget('user_id');
            cookie()->queue(cookie()->forget('remember_user'));
            return redirect()->route('login');
        }

        if (!Auth::check() || Auth::id() !== $user->id) {
            Auth::login($user);
        }
        $request->session()->put('user_id', $user->id);
        $request->setUserResolver(function () use ($user) {
            return $user; });
        return $next($request);
    }
}
