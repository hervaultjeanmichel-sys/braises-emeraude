<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verrou de préproduction (triple verrou SEO Com À Bord) :
 * 1. mot de passe (HTTP Basic) si PREPROD_USER / PREPROD_PASSWORD sont définis dans le .env ;
 * 2. en-tête X-Robots-Tag noindex hors production (en plus de la balise meta) ;
 * 3. robots.txt « Disallow: / » hors production (SitemapController::robots).
 * En production (APP_ENV=production, variables vides), ce middleware ne fait rien.
 */
class PreprodLock
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = (string) config('braises.preprod.user');
        $pass = (string) config('braises.preprod.password');

        if ($user !== '' && $pass !== ''
            && ! (hash_equals($user, (string) $request->getUser()) && hash_equals($pass, (string) $request->getPassword()))) {
            return response('Accès réservé', 401, ['WWW-Authenticate' => 'Basic realm="Preprod"', 'X-Robots-Tag' => 'noindex, nofollow']);
        }

        $response = $next($request);
        if (! app()->environment('production')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
