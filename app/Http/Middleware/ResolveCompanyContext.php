<?php

namespace App\Http\Middleware;

use App\Support\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = $request->query('company');

        // Livewire sends updates to a shared endpoint. Recover the company from
        // the page that initiated the update so separate tabs keep their own context.
        if (! $companyId && $request->hasHeader('X-Livewire')) {
            $referer = $request->headers->get('referer');
            $query = $referer ? parse_url($referer, PHP_URL_QUERY) : null;
            $parameters = [];

            if (is_string($query)) {
                parse_str($query, $parameters);
                $companyId = $parameters['company'] ?? null;
            }
        }

        if ($companyId) {
            app(CompanyContext::class)->resolve(
                Request::create('/?company='.urlencode((string) $companyId))
            );
        }

        return $next($request);
    }
}
