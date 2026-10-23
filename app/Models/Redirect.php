<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $guarded = ['id'];

    /** Normalise un chemin : slash initial et final, query conservée (?page_id=82). */
    public static function normalize(string $path): string
    {
        $q = '';
        if (str_contains($path, '?')) {
            [$path, $q] = explode('?', $path, 2);
            $q = '?'.$q;
        }
        $path = '/'.trim($path, '/');

        return ($path === '/' ? '/' : $path.'/').$q;
    }
}
