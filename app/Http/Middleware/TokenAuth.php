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
            \Log::warning('Mobile API: Missing bearer token', ['url' => $request->fullUrl()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = Token::where('token', $bearer)->first();
        if (!$token) {
            \Log::error('Mobile API: Invalid token', ['token' => substr($bearer, 0, 10) . '...', 'url' => $request->fullUrl()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = User::find($token->user_id);
        if (!$user) {
            \Log::error('Mobile API: User not found for token', ['user_id' => $token->user_id]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Use merge as it's more standard and accessible across controllers
        $request->merge(['auth_user' => $user]);
        $request->setUserResolver(function () use ($user) { return $user; });
        return $next($request);
    }
}
