<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En production uniquement : tout autre nom de domaine (ex. braises.comabord.com, l'ancienne préprod)
 * est redirigé en 301 vers le domaine officiel d'APP_URL (https://www.braisesemeraude.com) — pas de contenu dupliqué.
 */
class CanonicalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
        if (app()->environment('production') && $canonical !== '' && strcasecmp($request->getHost(), $canonical) !== 0) {
            return new RedirectResponse(rtrim((string) config('app.url'), '/').$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
