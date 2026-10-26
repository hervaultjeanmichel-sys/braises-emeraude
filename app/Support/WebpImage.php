<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/** Convertit une image du disque public en WebP (q80, 1600 px max) et renvoie le nouveau chemin. */
final class WebpImage
{
    public static function convert(string $path, int $maxWidth = 1600, int $quality = 80): string
    {
        $disk = Storage::disk('public');
        if (str_ends_with(strtolower($path), '.webp') || ! $disk->exists($path)) {
            return $path;
        }
        $img = @imagecreatefromstring((string) $disk->get($path));
        if (! $img) {
            return $path;
        }
        imagepalettetotruecolor($img);
        imagesavealpha($img, true);
        if (imagesx($img) > $maxWidth) {
            $img = imagescale($img, $maxWidth);
        }
        $new = preg_replace('/\.[a-z0-9]+$/i', '', $path).'.webp';
        ob_start();
        imagewebp($img, null, $quality);
        $disk->put($new, (string) ob_get_clean());
        imagedestroy($img);
        $disk->delete($path);

        return $new;
    }
}
