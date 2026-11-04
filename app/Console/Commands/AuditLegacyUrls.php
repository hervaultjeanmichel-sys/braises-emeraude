<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Audit SEO de la migration : relit le plan du site WordPress EN LIGNE (pages + images),
 * y ajoute la liste connue (database/data/legacy-urls.txt) et un export Search Console facultatif,
 * puis teste chaque URL sur le nouveau site : 200, ou 301 vers une page en 200 = OK.
 *
 *   php artisan braises:audit-urls https://braises.comabord.com   (verrou préprod lu dans le .env)
 *   … --extra=/home/nafa4741/gsc-pages.csv   (export « Pages » de Search Console, facultatif)
 *   … --no-images                            (pages seulement)
 *
 * Rapport complet : storage/app/audit-urls-AAAA-MM-JJ.txt
 */
class AuditLegacyUrls extends Command
{
    protected $signature = 'braises:audit-urls {base} {--user=} {--pass=} {--old=https://www.braisesemeraude.com} {--extra=} {--no-images} {--ip= : force l\'adresse IP du nouveau site (test depuis le serveur lui-même)}';

    protected $description = 'Compare toutes les URLs du site WordPress en ligne avec le nouveau site';

    /** @var array<string, string> chemin => source */
    private array $urls = [];

    /** @var string[] CURLOPT_RESOLVE (option --ip) */
    private array $resolve = [];

    public function handle(): int
    {
        $base = rtrim((string) $this->argument('base'), '/');
        $old = rtrim((string) $this->option('old'), '/');

        if ($ip = $this->option('ip')) {
            foreach ([$base, $old] as $u) {
                $host = (string) parse_url($u, PHP_URL_HOST);
                foreach (array_unique([$host, preg_replace('/^www\./', '', $host), 'www.'.preg_replace('/^www\./', '', $host)]) as $h) {
                    $this->resolve[] = $h.':443:'.$ip;
                    $this->resolve[] = $h.':80:'.$ip;
                }
            }
            $this->resolve = array_values(array_unique($this->resolve));
        }

        // 1. Plan du site WordPress en ligne (Yoast, sinon sitemap natif WordPress)
        $images = [];
        $sitemaps = $this->subSitemaps($old);
        foreach ($sitemaps as $sm) {
            $xml = $this->fetch($sm);
            foreach ($this->locs($xml, 'loc') as $u) {
                if (! str_ends_with(strtolower($u), '.xml')) {
                    $this->add($u, 'sitemap');
                }
            }
            foreach ($this->locs($xml, 'image:loc') as $u) {
                $images[$this->path($u)] = true;
            }
        }
        $fromSitemap = count($this->urls);
        $this->info(count($sitemaps).' plans lus sur '.$old.' : '.$fromSitemap.' pages, '.count($images).' images');

        // 2. Liste connue + export Search Console
        $known = [];
        foreach (file(base_path('database/data/legacy-urls.txt')) ?: [] as $line) {
            $line = trim($line);
            if ($line !== '' && $line[0] !== '#') {
                $known[$this->path($line)] = true;
                $this->add($line, 'liste connue');
            }
        }
        if ($extra = $this->option('extra')) {
            preg_match_all('#https?://[^\s",;]+#', (string) @file_get_contents($extra), $m);
            foreach ($m[0] as $u) {
                str_contains($u, 'braisesemeraude') && $this->add($u, 'Search Console');
            }
            $this->info('Export Search Console : '.count($m[0]).' URLs lues');
        }

        $newOnes = array_keys(array_diff_key(array_filter($this->urls, fn ($s) => $s !== 'liste connue'), $known));

        // 3. Test sur le nouveau site
        // Identifiants du verrou préprod : options, sinon ceux du .env
        $user = $this->option('user') ?: config('braises.preprod.user');
        $pass = $this->option('pass') ?: config('braises.preprod.password');
        $resolve = $this->resolve;
        $client = fn () => Http::timeout(20)->withoutRedirecting()
            ->when($resolve, fn ($h) => $h->withOptions(['curl' => [CURLOPT_RESOLVE => $resolve]]))
            ->when($user, fn ($h) => $h->withBasicAuth((string) $user, (string) $pass));

        $ko = [];
        $okCount = 0;
        $tests = $this->urls;
        if (! $this->option('no-images')) {
            foreach (array_keys($images) as $img) {
                $tests[$img] = 'image';
            }
        }
        $bar = $this->output->createProgressBar(count($tests));
        foreach ($tests as $path => $source) {
            $bar->advance();
            $res = $client()->get($base.$path);
            $code = $res->status();
            $final = '';
            $ok = $code === 200;
            if (in_array($code, [301, 308], true)) {
                $loc = (string) $res->header('Location');
                $target = str_starts_with($loc, 'http') ? $loc : $base.$loc;
                $finalCode = $client()->get($target)->status();
                $final = $this->path($target)." ({$finalCode})";
                // Une URL du plan du site doit répondre 200 directement (pas de redirection dans un sitemap)
                $ok = $finalCode === 200 && $source !== 'sitemap';
            }
            $ok ? $okCount++ : $ko[] = "KO  {$code}  {$path}  [{$source}]".($final ? "  → {$final}" : '');
        }
        $bar->finish();
        $this->newLine(2);

        // 4. Rapport
        $report = ['AUDIT URLs '.now()->format('d/m/Y H:i').' — ancien site '.$old.' → '.$base, ''];
        $report[] = 'URLs du site en ligne ABSENTES de la liste connue ('.count($newOnes).') :';
        foreach ($newOnes as $p) {
            $report[] = '  '.$p.'  ['.$this->urls[$p].']';
        }
        $report[] = '';
        $report[] = 'KO ('.count($ko).') :';
        array_push($report, ...array_map(fn ($l) => '  '.$l, $ko));
        $file = storage_path('app/audit-urls-'.now()->format('Y-m-d').'.txt');
        file_put_contents($file, implode("\n", $report)."\n");

        foreach (array_slice($ko, 0, 40) as $line) {
            $this->line($line);
        }
        count($ko) > 40 && $this->line('… ('.(count($ko) - 40).' autres dans le rapport)');
        $this->line('NOUVELLES URLs trouvées : '.count($newOnes));
        $this->line('RESUME : '.$okCount.'/'.count($tests).' OK — rapport : '.$file);

        return $ko === [] ? self::SUCCESS : self::FAILURE;
    }

    /** @return string[] */
    private function subSitemaps(string $old): array
    {
        foreach (['/sitemap_index.xml', '/wp-sitemap.xml', '/sitemap.xml'] as $index) {
            $xml = $this->fetch($old.$index);
            $locs = $this->locs($xml, 'loc');
            if ($locs) {
                $subs = array_values(array_filter($locs, fn ($u) => str_ends_with(strtolower(strtok($u, '?')), '.xml')));

                return $subs ?: [$old.$index];
            }
        }
        $this->warn('Aucun plan du site lisible sur '.$old);

        return [];
    }

    private function fetch(string $url): string
    {
        // Lecture seule du plan du site public : si le certificat SSL de l'ancien site est invalide
        // (constaté le 04/11/2026 : certificat lb2.levillage.org), on relit sans vérification.
        foreach ([false, true] as $insecure) {
            try {
                $req = Http::timeout(30)->withUserAgent('BraisesEmeraude-audit/1.0')
                    ->when($this->resolve, fn ($h) => $h->withOptions(['curl' => [CURLOPT_RESOLVE => $this->resolve]]));
                $res = ($insecure ? $req->withoutVerifying() : $req)->get($url);
                if ($insecure) {
                    $this->warn('Certificat SSL invalide sur '.parse_url($url, PHP_URL_HOST).' : lecture sans vérification');
                }

                return $res->ok() ? $res->body() : '';
            } catch (\Throwable) {
                continue;
            }
        }

        return '';
    }

    /** @return string[] */
    private function locs(string $xml, string $tag): array
    {
        preg_match_all('#<'.preg_quote($tag, '#').'>\s*(?:<!\[CDATA\[)?\s*(.*?)\s*(?:\]\]>)?\s*</'.preg_quote($tag, '#').'>#s', $xml, $m);

        return array_map(fn ($u) => html_entity_decode(trim($u)), $m[1]);
    }

    private function add(string $url, string $source): void
    {
        $p = $this->path($url);
        $this->urls[$p] ??= $source;
    }

    private function path(string $url): string
    {
        $parts = parse_url($url);
        $p = $parts['path'] ?? '/';
        if (isset($parts['query'])) {
            $p .= '?'.$parts['query'];
        }

        return $p === '' ? '/' : $p;
    }
}
