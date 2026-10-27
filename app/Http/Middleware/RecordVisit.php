<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Compte les visites du site public pour le tableau de bord. Sans cookie, sans donnée personnelle conservée.
 * Ignorés : robots, admin, appels JSON / fichiers, réponses autres que 200 HTML, préchargements.
 */
class RecordVisit
{
    private const BOTS = '/bot|crawl|spider|slurp|bing|google|yandex|baidu|duckduck|facebookexternalhit|preview|monitor|uptime|curl|wget|python|php|headless|lighthouse|pingdom|ahrefs|semrush|mj12/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->countable($request, $response)) {
                $this->record($request);
            }
        } catch (\Throwable $e) {
            Log::debug('Mesure d’audience ignorée', ['error' => $e->getMessage()]); // ne casse jamais la page
        }

        return $response;
    }

    private function countable(Request $request, Response $response): bool
    {
        $ua = (string) $request->userAgent();

        return $request->isMethod('GET')
            && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && ! $request->is('admin', 'admin/*', 'livewire/*', 'rendez-vous/creneaux', 'rendez-vous/zone')
            && ! $request->headers->has('Purpose') && ! $request->headers->has('Sec-Purpose') // préchargements
            && $ua !== '' && ! preg_match(self::BOTS, $ua);
    }

    private function record(Request $request): void
    {
        $day = now('Europe/Paris')->toDateString();
        $path = mb_substr('/'.ltrim($request->path(), '/'), 0, 190);
        $path = $path === '//' ? '/' : $path;
        $visitor = hash('sha256', $day.'|'.$request->ip().'|'.$request->userAgent().'|'.config('app.key'));
        $now = now();

        DB::statement('INSERT INTO page_views (day, path, views) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE views = views + 1', [$day, $path]);

        $updated = DB::table('visits')->where('day', $day)->where('visitor', $visitor)
            ->update(['pages' => DB::raw('pages + 1'), 'last_seen_at' => $now]);
        if (! $updated) {
            DB::table('visits')->insertOrIgnore([
                'day' => $day, 'visitor' => $visitor, 'source' => $this->source($request),
                'device' => preg_match('/Mobi|Android|iPhone|iPad/i', (string) $request->userAgent()) ? 'mobile' : 'ordinateur',
                'landing' => $path, 'pages' => 1, 'last_seen_at' => $now,
            ]);
        }
    }

    private function source(Request $request): string
    {
        $campaign = (string) $request->query('utm_campaign', '');
        if ($campaign !== '') {
            return mb_substr('campagne:'.strtolower($campaign), 0, 30);
        }
        if ($src = $request->query('utm_source')) {
            return mb_substr(strtolower((string) $src), 0, 30);
        }
        $ref = (string) parse_url((string) $request->headers->get('referer', ''), PHP_URL_HOST);

        return match (true) {
            $ref === '' || $ref === $request->getHost() => 'direct',
            str_contains($ref, 'google.') => 'google',
            str_contains($ref, 'bing.') || str_contains($ref, 'qwant') || str_contains($ref, 'duckduckgo') || str_contains($ref, 'ecosia') => 'autres moteurs',
            (bool) preg_match('/facebook|instagram|linkedin|pinterest|tiktok|t\.co|twitter|x\.com/', $ref) => 'réseaux sociaux',
            default => 'sites référents',
        };
    }
}
