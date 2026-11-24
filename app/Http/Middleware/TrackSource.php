<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Mémorise la provenance de la première visite (UTM, site référent) pour qualifier les demandes. */
class TrackSource
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->hasSession()) {
            $utm = array_filter($request->only(['utm_source', 'utm_medium', 'utm_campaign']));
            if ($utm !== []) {
                $request->session()->put('utm', $utm);
            }
            // Code promo arrivé par un lien (?code=SISKO, bouton du volet promo) → pré-remplit les formulaires
            $code = strtoupper(trim((string) $request->query('code', '')));
            if ($code !== '' && preg_match('/^[A-Z0-9_-]{2,40}$/', $code)) {
                $request->session()->put('promo_code', $code);
            }
            $ref = (string) $request->headers->get('referer', '');
            if ($ref !== '' && ! $request->session()->has('referer') && parse_url($ref, PHP_URL_HOST) !== $request->getHost()) {
                $request->session()->put('referer', mb_substr($ref, 0, 255));
            }
        }

        return $next($request);
    }
}
