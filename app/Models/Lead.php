<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'utm' => 'array',
            'products' => 'array',
            'photos' => 'array',
            'consent_at' => 'datetime',
            'exported_at' => 'datetime',
        ];
    }

    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'rappelee' => 'Rappelée',
        'visite' => 'Visite planifiée',
        'devis_envoye' => 'Devis envoyé',
        'signee' => 'Signée',
        'perdue' => 'Perdue',
    ];

    public const TYPES = [
        'devis' => 'Devis',
        'contact' => 'Contact',
        'concours' => 'Concours',
        'rdv' => 'Rendez-vous',
    ];

    protected static function booted(): void
    {
        // Routage automatique vers le bon magasin selon le code postal
        static::creating(function (self $lead) {
            $lead->store_id ??= Store::forPostalCode($lead->postal_code)?->id;
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }
}
