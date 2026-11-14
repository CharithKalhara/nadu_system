<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMobileTokenAbility
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'This token is not authorised for mobile entry.'], 403);
        }

        $token = $user->currentAccessToken();

        if ($token && ! $token->can('mobile:use')) {
            return response()->json(['message' => 'This token is not authorised for mobile entry.'], 403);
        }

        return $next($request);
    }
}
