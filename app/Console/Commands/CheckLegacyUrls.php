<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Recette des anciennes URLs : chacune doit répondre 200, ou 301 vers une page en 200.
 *
 *   php artisan braises:check-urls https://preprod.comabord.com --user=xxx --pass=yyy
 *   php artisan braises:check-urls https://www.braisesemeraude.com
 */
class CheckLegacyUrls extends Command
{
    protected $signature = 'braises:check-urls {base} {--user=} {--pass=} {--file=database/data/legacy-urls.txt}';

    protected $description = 'Teste les ~149 URLs WordPress conservées (200 ou 301 attendus)';

    public function handle(): int
    {
        $base = rtrim((string) $this->argument('base'), '/');
        $paths = array_filter(array_map('trim', file(base_path($this->option('file')))), fn ($l) => $l !== '' && $l[0] !== '#');

        $client = fn () => Http::timeout(20)->withoutRedirecting()
            ->when($this->option('user'), fn ($h) => $h->withBasicAuth($this->option('user'), (string) $this->option('pass')));

        $rows = [];
        $ko = 0;
        foreach ($paths as $path) {
            $res = $client()->get($base.$path);
            $code = $res->status();
            $final = '';
            $ok = $code === 200;
            if (in_array($code, [301, 308], true)) {
                $location = (string) $res->header('Location');
                $target = str_starts_with($location, 'http') ? $location : $base.$location;
                $finalCode = $client()->get($target)->status();
                $final = parse_url($target, PHP_URL_PATH)." ({$finalCode})";
                $ok = $finalCode === 200;
            }
            if (! $ok) {
                $ko++;
            }
            $rows[] = [$ok ? 'OK' : 'KO', $code, $path, $final];
        }

        $this->table(['', 'Code', 'Ancienne URL', 'Destination'], $rows);
        $total = count($paths);
        $this->line("RESUME : ".($total - $ko)."/{$total} OK");

        return $ko === 0 ? self::SUCCESS : self::FAILURE;
    }
}
