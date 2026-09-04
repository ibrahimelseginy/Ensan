<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PersonalAccessToken;
use Carbon\Carbon;

class AnasenApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $tokenStr = str_replace('Bearer ', '', $header);
        
        $token = PersonalAccessToken::where('token', hash('sha256', $tokenStr))->first();

        if (!$token || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired token'], 401);
        }

        $token->last_used_at = Carbon::now();
        $token->save();

        // Attach the user to the request
        $request->setUserResolver(function () use ($token) {
            return $token->tokenable;
        });

        return $next($request);
    }
}
