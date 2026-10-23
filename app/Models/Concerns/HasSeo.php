<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Champs SEO communs : seo_title, seo_description, robots, is_published.
 * Chaque modèle public implémente url() (chemin avec slash final, comme WordPress).
 */
trait HasSeo
{
    abstract public function url(): string;

    public function metaTitle(): string
    {
        return $this->seo_title ?: ($this->h1 ?? $this->title ?? $this->name ?? '');
    }

    public function metaDescription(): ?string
    {
        return $this->seo_description ?: (isset($this->intro) ? str($this->intro)->stripTags()->limit(155)->toString() : null);
    }

    public function isIndexable(): bool
    {
        return $this->is_published && ! str_contains((string) $this->robots, 'noindex');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }
}
