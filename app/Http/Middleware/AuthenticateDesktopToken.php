<?php

namespace App\Http\Middleware;

use App\Models\DesktopToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDesktopToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainTextToken = $request->bearerToken();

        if (! is_string($plainTextToken) || $plainTextToken === '') {
            return response()->json(['message' => 'Please sign in again.'], 401);
        }

        $token = DesktopToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainTextToken))
            ->where('expires_at', '>', now())
            ->first();

        if (! $token || ! $token->user) {
            return response()->json(['message' => 'Your desktop session expired. Please sign in again.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        Auth::guard('web')->setUser($token->user);
        $request->setUserResolver(fn () => $token->user);

        return $next($request);
    }
}
