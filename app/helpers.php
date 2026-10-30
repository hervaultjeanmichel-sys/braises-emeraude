<?php

declare(strict_types=1);

use App\Models\Setting;

if (! function_exists('s')) {
    /** Texte éditable depuis l'admin (Réglages › Textes du site). Le défaut sert au premier affichage. */
    function s(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('media')) {
    function media(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : '/storage/'.$path;
    }
}

if (! function_exists('em')) {
    /** Titre éditable : *mot* devient <em>mot</em> (accent émeraude), le reste est échappé. */
    function em(?string $text): string
    {
        return preg_replace('/\*(.+?)\*/u', '<em>$1</em>', e((string) $text));
    }
}

if (! function_exists('menu')) {
    /** Menu éditable dans Réglages : une ligne « Libellé|/url/ » par entrée. @return array<int, array{label:string,url:string}> */
    function menu(string $key, string $default = ''): array
    {
        // Pages hors ligne : leur lien disparaît automatiquement des menus.
        static $offline = null;
        $offline ??= \App\Models\Page::where('is_published', false)->get(['slug'])
            ->map(fn ($p) => $p->url())->all();

        $items = [];
        foreach (preg_split('/\R/', (string) s($key, $default)) as $line) {
            if (str_contains($line, '|')) {
                [$label, $url] = array_map('trim', explode('|', $line, 2));
                $path = '/'.trim((string) parse_url($url, PHP_URL_PATH), '/').'/';
                if (in_array(str_replace('//', '/', $path), $offline, true)) {
                    continue;
                }
                $items[] = ['label' => $label, 'url' => $url];
            }
        }

        return $items;
    }
}

if (! function_exists('tel')) {
    function tel(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return str_starts_with($digits, '0') ? '+33'.substr($digits, 1) : $digits;
    }
}
