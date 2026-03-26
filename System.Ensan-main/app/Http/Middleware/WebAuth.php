<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->session()->get('user_id');
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
        $user = User::find($id);
        if (!$user || !$user->active || !$user->is_employee) {
            $request->session()->forget('user_id');
            cookie()->queue(cookie()->forget('remember_user'));
            return redirect()->route('login');
        }
        \Illuminate\Support\Facades\Auth::login($user);
        $request->setUserResolver(function () use ($user) {
            return $user; });
        return $next($request);
    }
}
