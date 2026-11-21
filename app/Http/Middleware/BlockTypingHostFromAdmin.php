<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockTypingHostFromAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $typingDomain = config('app.typing_domain');

        if (strcasecmp($request->getHost(), $typingDomain) === 0) {
            abort(404);
        }

        return $next($request);
    }
}
