<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\WebpImage;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Store extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (self $s) {
            if (is_array($s->photos)) {
                $s->photos = array_values(array_map(fn ($p) => WebpImage::convert((string) $p), array_filter($s->photos)));
            }
        });
    }

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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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

    /** Adresse courte façon maquette : « La Hertaudière, 35120 », « Pleurtuit (35730) », « Rennes Métropole ». */
    /** URL d'intégration (YouTube sans cookie / Vimeo dnt) d'un lien vidéo collé dans l'admin. */
    public function videoEmbedUrl(): ?string
    {
        $u = (string) $this->video_url;
        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/|live/))([\w-]{11})~', $u, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?autoplay=1&rel=0';
        }
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $u, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1&dnt=1';
        }

        return null;
    }

    /** Vignette de la vidéo : image d'attente de l'admin, sinon miniature YouTube. */
    public function videoThumb(): ?string
    {
        if ($this->video_poster) {
            return media($this->video_poster);
        }

        return preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/|live/))([\w-]{11})~', (string) $this->video_url, $m)
            ? 'https://i.ytimg.com/vi/'.$m[1].'/hqdefault.jpg' : null;
    }

    /** Lien d'itinéraire Google Maps (coordonnées si connues, sinon nom + adresse). */
    public function directionsUrl(): string
    {
        return 'https://www.google.com/maps/search/?api=1&query='.($this->latitude
            ? $this->latitude.','.$this->longitude
            : urlencode($this->name.' '.$this->addressLine()));
    }

    /** Repère pour les cartes (public/js/map.js). */
    public function mapMarker(): array
    {
        return [
            'lat' => (float) $this->latitude, 'lng' => (float) $this->longitude,
            'name' => $this->name, 'short' => $this->zone_label ?: $this->short_name,
            'address' => $this->addressLine(), 'phone' => $this->phone,
            'url' => $this->url(), 'directions' => $this->directionsUrl(), 'isNew' => (bool) $this->is_new,
        ];
    }

    public function addressLine(): string
    {
        if ($this->address) {
            return trim($this->address.', '.trim($this->postal_code.' '.$this->city), ', ');
        }
        if ($this->postal_code) {
            return trim($this->city.' ('.$this->postal_code.')');
        }

        return (string) ($this->area_label ?: $this->city);
    }

    /** Résumé lisible des horaires : ["Lun–ven 9h–12h · 14h–18h", "Sam 9h–12h30"]. */
    public function hoursSummary(): array
    {
        $days = [1 => 'lun', 2 => 'mar', 3 => 'mer', 4 => 'jeu', 5 => 'ven', 6 => 'sam', 7 => 'dim'];
        $fmt = function (string $t): string {
            [$h, $m] = array_pad(explode(':', $t), 2, '00');

            return ((int) $h).'h'.($m === '00' ? '' : $m);
        };
        $byDay = [];
        foreach ($this->opening_hours ?? [] as $slot) {
            $byDay[(int) $slot['day']][] = $fmt($slot['opens']).'–'.$fmt($slot['closes']);
        }
        ksort($byDay);
        $groups = [];
        foreach ($byDay as $day => $slots) {
            $key = implode(' · ', $slots);
            $last = array_key_last($groups);
            if ($last !== null && $groups[$last]['key'] === $key && $groups[$last]['to'] === $day - 1) {
                $groups[$last]['to'] = $day;
            } else {
                $groups[] = ['from' => $day, 'to' => $day, 'key' => $key];
            }
        }

        return array_map(function ($g) use ($days) {
            $label = ucfirst($days[$g['from']]).($g['to'] > $g['from'] ? '–'.$days[$g['to']] : '');

            return $label.' '.$g['key'];
        }, $groups);
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
