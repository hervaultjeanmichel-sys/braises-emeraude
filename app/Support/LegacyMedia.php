<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Correspondance ancienne URL WordPress d'un média → copie locale (même règle de nommage que braises:clean-html). */
class LegacyMedia
{
    public static function localUrl(string $oldPath): ?string
    {
        $path = (string) parse_url($oldPath, PHP_URL_PATH);
        $disk = Storage::disk('public');

        if (preg_match('/\.pdf$/i', $path)) {
            $name = 'wp/pdf/'.Str::slug(pathinfo($path, PATHINFO_FILENAME)).'.pdf';

            return $disk->exists($name) ? '/storage/'.$name : null;
        }

        $base = preg_replace('/-\d+x\d+$/', '', pathinfo($path, PATHINFO_FILENAME));
        $name = 'wp/'.Str::slug((string) $base).'-'.substr(md5(dirname($path)), 0, 6).'.webp';

        return $disk->exists($name) ? '/storage/'.$name : null;
    }
}
