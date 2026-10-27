<x-filament-panels::page>
    @if(! $enabled)
        <x-filament::section>
            <p><strong>Clé Google manquante.</strong> Ajoutez <code>GOOGLE_PLACES_KEY</code> dans le fichier .env du serveur (clé Google Cloud avec « Places API (New) » activée), puis lancez <code>php artisan braises:google-reviews --find</code>.</p>
        </x-filament::section>
    @else
        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
            @if($summary)
                <div style="display:flex;align-items:baseline;gap:.75rem">
                    <span style="font-size:3rem;font-weight:800;line-height:1">{{ number_format((float) $summary['rating'], 1, ',', '') }}</span>
                    <span>/ 5 — moyenne Google du groupe sur <strong>{{ $summary['count'] }}</strong> avis</span>
                </div>
            @endif
            <x-filament::button wire:click="refreshFromGoogle" icon="heroicon-o-arrow-path" color="gray">Recharger depuis Google</x-filament::button>
        </div>
        <p style="opacity:.7;font-size:.875rem">Google fournit les 5 avis les plus pertinents de chaque fiche. La note et le nombre d’avis sont ceux calculés par Google sur l’ensemble des avis : masquer un avis ne change pas la note affichée.</p>

        @foreach($stores as $store)
            @php($d = $data[$store->id] ?? null)
            <x-filament::section :heading="$store->area_label ?: $store->short_name" :description="$d ? number_format((float) $d['rating'], 1, ',', '').' / 5 — '.$d['count'].' avis Google' : ($store->google_place_id ? 'Avis indisponibles pour le moment' : 'Identifiant Google manquant : php artisan braises:google-reviews '.$store->short_name.' --find')">
                @if($d && $d['reviews'])
                    <div style="display:grid;gap:.75rem">
                        @foreach($d['reviews'] as $r)
                            <div style="display:grid;grid-template-columns:40px 1fr auto;gap:.85rem;align-items:start;padding:.75rem;border-radius:.75rem;border:1px solid rgba(127,127,127,.2);{{ $r['visible'] ? '' : 'opacity:.45' }}">
                                @if($r['photo'])<img src="{{ $r['photo'] }}" alt="" width="40" height="40" style="border-radius:50%" referrerpolicy="no-referrer">@else<span></span>@endif
                                <div>
                                    <div><strong>{{ $r['author'] }}</strong> <span style="color:#f59e0b">{{ str_repeat('★', $r['rating']) }}</span><span style="opacity:.3">{{ str_repeat('★', 5 - $r['rating']) }}</span> <span style="opacity:.6;font-size:.8rem">{{ $r['when'] }}</span>@if($r['featured']) <x-filament::badge color="success" style="display:inline-flex">Mis en avant</x-filament::badge>@endif</div>
                                    <p style="font-size:.875rem;margin-top:.25rem">{{ $r['text'] ?: '(note sans commentaire)' }}</p>
                                </div>
                                <div style="display:grid;gap:.4rem">
                                    <x-filament::button size="xs" :color="$r['visible'] ? 'success' : 'gray'" wire:click="toggleVisible({{ $r['store_id'] }}, '{{ $r['key'] }}', {{ $r['visible'] ? 'true' : 'false' }})">{{ $r['visible'] ? 'Affiché' : 'Masqué' }}</x-filament::button>
                                    <x-filament::button size="xs" color="gray" outlined wire:click="toggleFeatured({{ $r['store_id'] }}, '{{ $r['key'] }}', {{ $r['featured'] ? 'true' : 'false' }})">{{ $r['featured'] ? '★ En avant' : 'Mettre en avant' }}</x-filament::button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @endforeach
    @endif
</x-filament-panels::page>
