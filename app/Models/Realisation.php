<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\WebpImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Realisation extends Model
{
    protected $guarded = ['id'];

    public const SOURCES = ['google' => 'Avis Google', 'facebook' => 'Facebook', 'questionnaire' => 'Questionnaire de satisfaction', 'direct' => 'Recueilli par l’équipe'];

    public const ENERGIES = ['bois' => 'Bois', 'granules' => 'Granulés', 'mixte' => 'Mixte', 'gaz' => 'Gaz'];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'installed_on' => 'date',
            'review_date' => 'date',
            'review_consent' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // photos ajoutées dans l'admin → WebP 1600 px
        static::saving(function (self $r) {
            if (is_array($r->photos)) {
                $r->photos = array_values(array_map(fn ($p) => WebpImage::convert((string) $p), array_filter($r->photos)));
            }
        });
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** L'avis ne s'affiche que s'il existe ET que le client a donné son accord. */
    public function hasPublicReview(): bool
    {
        return $this->review_consent && filled($this->review_text);
    }

    public function cover(): ?string
    {
        return $this->photos[0] ?? null;
    }
}
