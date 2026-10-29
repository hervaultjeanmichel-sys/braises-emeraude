<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\WebpImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PromoSlide extends Model
{
    protected $guarded = ['id'];

    public const SCOPES = ['all' => 'Toutes les pages', 'home' => 'Accueil uniquement'];

    public const POSITIONS = ['center' => 'Centre de l\'écran', 'bottom-left' => 'En bas à gauche'];

    public const FREQUENCIES = [
        'session' => 'Une fois par visite',
        'day' => 'Une fois par jour',
        'week' => 'Une fois par semaine',
        'always' => 'À chaque page',
    ];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        // image ajoutée dans l'admin → WebP 1200 px
        static::saving(function (self $s) {
            if ($s->image) {
                $s->image = WebpImage::convert((string) $s->image, 1200);
            }
        });
    }

    /** Volet en cours pour cette page (le premier dans l'ordre), ou null. */
    public static function current(bool $isHome): ?self
    {
        if (! Schema::hasTable('promo_slides')) {
            return null;
        }

        return static::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->when(! $isHome, fn ($q) => $q->where('scope', 'all'))
            ->orderBy('sort')->latest('id')->first();
    }

    /** Fond clair → texte foncé, fond foncé → texte clair (luminance WCAG). */
    public function isLightBackground(): bool
    {
        $hex = ltrim((string) $this->bg_color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (! preg_match('/^[0-9a-f]{6}/i', $hex)) {
            return true;
        }
        $lin = function (int $c): float {
            $c /= 255;

            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        };
        $l = 0.2126 * $lin(hexdec(substr($hex, 0, 2))) + 0.7152 * $lin(hexdec(substr($hex, 2, 2))) + 0.0722 * $lin(hexdec(substr($hex, 4, 2)));

        return $l > 0.35;
    }

    /** Clé de mémorisation côté visiteur : change si le volet est modifié. */
    public function memoKey(): string
    {
        return $this->id.'-'.($this->updated_at?->timestamp ?? 0);
    }
}
