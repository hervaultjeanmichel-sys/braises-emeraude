<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/** Chiffre clé du bandeau « rassurance » affiché avant le pied de page. */
class KeyFigure extends Model
{
    public const SOURCES = [
        'manual' => 'Valeur saisie',
        'brands_count' => 'Automatique : nombre de marques publiées',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'animate' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** Valeur affichée : saisie, ou calculée en base selon la source. */
    public function displayValue(): int
    {
        return match ($this->source) {
            'brands_count' => Brand::where('is_published', true)->count(),
            default => (int) $this->value,
        };
    }

    /** Chiffres actifs pour le front (une requête légère, toujours à jour après une modification dans l'admin). */
    public static function forFront(): Collection
    {
        return static::where('is_active', true)->orderBy('sort')->get();
    }
}
