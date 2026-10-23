<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Store extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'closed_dates' => 'array',
            'served_communes' => 'array',
            'served_postcodes' => 'array',
            'photos' => 'array',
            'team' => 'array',
            'booking_settings' => 'array',
            'is_new' => 'boolean',
            'booking_enabled' => 'boolean',
            'is_published' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function url(): string
    {
        return '/'.$this->slug.'/';
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /** Ouvert / fermé en temps réel (fuseau Europe/Paris). */
    public function isOpenNow(?Carbon $now = null): bool
    {
        $now ??= now('Europe/Paris');
        if (in_array($now->toDateString(), $this->closed_dates ?? [], true)) {
            return false;
        }
        $time = $now->format('H:i');
        foreach ($this->opening_hours ?? [] as $slot) {
            if ((int) ($slot['day'] ?? 0) === $now->dayOfWeekIso
                && $time >= ($slot['opens'] ?? '99') && $time < ($slot['closes'] ?? '00')) {
                return true;
            }
        }

        return false;
    }

    /** Magasin destinataire selon le code postal (repli : département 35 → Saint-Malo). */
    public static function forPostalCode(?string $cp): ?self
    {
        if (! $cp) {
            return null;
        }
        $cp = trim($cp);
        foreach (static::published()->orderBy('sort')->get() as $store) {
            if (in_array($cp, $store->served_postcodes ?? [], true)) {
                return $store;
            }
        }

        return static::published()->orderBy('sort')->first();
    }
}
