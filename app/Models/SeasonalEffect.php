<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SeasonalEffect extends Model
{
    protected $guarded = ['id'];

    public const EFFECTS = [
        'snow' => '❄️ Neige (Noël, hiver)',
        'fireworks' => '🎆 Feux d’artifice (Nouvel an)',
        'halloween' => '🎃 Halloween (chauves-souris, citrouilles)',
        'easter' => '🥚 Pâques (œufs, fleurs)',
        'hearts' => '❤️ Cœurs (Saint-Valentin)',
        'leaves' => '🍂 Feuilles d’automne',
        'confetti' => '🎉 Confettis (inauguration, anniversaire)',
        'embers' => '🔥 Braises qui montent (marque)',
    ];

    public const INTENSITIES = ['low' => 'Discret', 'medium' => 'Moyen', 'high' => 'Intense'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'yearly' => 'boolean', 'is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('seasonal.current'));
        static::deleted(fn () => Cache::forget('seasonal.current'));
    }

    /** Actif à cette date ? En mode annuel, seules les dates (jour/mois) comptent, y compris à cheval sur deux années. */
    public function isOn(CarbonImmutable $day): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if (! $this->yearly) {
            return $day->betweenIncluded($this->starts_on->startOfDay(), $this->ends_on->endOfDay());
        }
        $md = $day->format('m-d');
        $from = $this->starts_on->format('m-d');
        $to = $this->ends_on->format('m-d');

        return $from <= $to ? ($md >= $from && $md <= $to) : ($md >= $from || $md <= $to);
    }

    /** Effet du jour (le premier dans l'ordre), mis en cache jusqu'à minuit. */
    public static function current(): ?self
    {
        if (! Schema::hasTable('seasonal_effects')) {
            return null;
        }
        $id = Cache::remember('seasonal.current', now()->endOfDay(), function () {
            $today = CarbonImmutable::now('Europe/Paris');

            return static::where('is_active', true)->orderBy('sort')->get()->first(fn (self $e) => $e->isOn($today))?->id ?? 0;
        });

        return $id ? static::find($id) : null;
    }
}
