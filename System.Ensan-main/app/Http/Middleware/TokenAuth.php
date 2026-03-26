<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\User;

class TokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = Token::where('token', $bearer)->first();
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = User::find($token->user_id);
        $request->attributes->set('auth_user', $user);
        $request->setUserResolver(function () use ($user) { return $user; });
        return $next($request);
    }
}
