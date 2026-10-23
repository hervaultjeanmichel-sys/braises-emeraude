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
