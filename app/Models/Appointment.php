<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = ['en_attente' => 'En attente', 'confirme' => 'Confirmé', 'refuse' => 'Refusé', 'annule' => 'Annulé'];

    /** Statuts qui occupent un créneau. */
    public const BLOCKING = ['en_attente', 'confirme'];

    protected function casts(): array
    {
        return [
            'at_home' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'consent_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeBlocking(Builder $q): Builder
    {
        return $q->whereIn('status', self::BLOCKING);
    }

    public function whenLabel(): string
    {
        return ucfirst($this->starts_at->locale('fr')->isoFormat('dddd D MMMM YYYY [à] H[h]mm'));
    }

    public function placeLabel(): string
    {
        return $this->at_home
            ? trim(($this->address ? $this->address.', ' : '').$this->postal_code.' '.$this->city)
            : ($this->store?->name.' — '.$this->store?->addressLine());
    }

    /** Fichier agenda .ics (le client et l'équipe l'ajoutent à leur agenda en un clic). */
    public function ics(): string
    {
        $fmt = fn ($d) => $d->copy()->utc()->format('Ymd\THis\Z');
        $esc = fn (string $s) => addcslashes(str_replace(["\r\n", "\n"], '\n', $s), ',;');

        return implode("\r\n", [
            'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//Braises Emeraude//Rendez-vous//FR', 'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:rdv-'.$this->id.'@braisesemeraude.com',
            'DTSTAMP:'.$fmt(now()),
            'DTSTART:'.$fmt($this->starts_at),
            'DTEND:'.$fmt($this->ends_at),
            'SUMMARY:'.$esc($this->type_label.' — '.($this->store?->name ?? 'Braises Émeraude')),
            'LOCATION:'.$esc($this->placeLabel()),
            'DESCRIPTION:'.$esc('Rendez-vous Braises Émeraude'.($this->store?->phone ? ' — '.$this->store->phone : '')),
            'END:VEVENT', 'END:VCALENDAR', '',
        ]);
    }
}
