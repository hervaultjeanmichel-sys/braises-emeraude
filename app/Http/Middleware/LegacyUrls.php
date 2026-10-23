<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reprise des URLs WordPress :
 * 1. redirections 301 gérées dans l'admin (table redirects), y compris /?page_id=82 ;
 * 2. slash final imposé (format WordPress) pour éviter le contenu dupliqué.
 */
class LegacyUrls
{
    private const SKIP = ['admin', 'livewire', 'filament', 'storage', 'up', 'build', 'css', 'js', 'img'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = '/'.ltrim($request->getPathInfo(), '/');
        $first = explode('/', trim($path, '/'))[0] ?? '';
        if (in_array($first, self::SKIP, true) || pathinfo($path, PATHINFO_EXTENSION) !== '') {
            return $next($request);
        }

        // 1. Redirection enregistrée (avec query string d'abord : ?page_id=82)
        $qs = $request->getQueryString();
        $candidates = array_filter([
            $qs ? Redirect::normalize($path.'?'.$qs) : null,
            Redirect::normalize($path),
        ]);
        $redirect = Redirect::whereIn('from_path', $candidates)->first();
        if ($redirect) {
            $redirect->increment('hits', 1, ['last_hit_at' => now()]);
            if ($redirect->status_code === 410) {
                abort(410);
            }

            return redirect($redirect->to_path, $redirect->status_code);
        }

        // 2. Slash final
        if ($path !== '/' && ! str_ends_with($path, '/')) {
            return redirect($path.'/'.($qs ? '?'.$qs : ''), 301);
        }

        return $next($request);
    }
}
