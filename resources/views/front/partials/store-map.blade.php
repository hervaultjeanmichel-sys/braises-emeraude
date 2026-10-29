{{-- Carte des magasins (OpenStreetMap + Leaflet via public/js/map.js, chargée à l'approche).
     $mapStores : collection de magasins (un seul sur la page magasin, tous sur l'accueil).
     $currentPage : true pour masquer le lien « Voir le magasin » du magasin affiché. --}}
@php($mapStores = collect($mapStores ?? [])->filter(fn ($st) => $st->latitude && $st->longitude))
@php($mapMarkers = $mapStores->map(fn ($st) => ($currentPage ?? false) ? array_merge($st->mapMarker(), ['url' => null]) : $st->mapMarker())->values()->all())
@php($mapLabels = ['directions' => s('store.directions', 'Itinéraire'), 'page' => s('store.map_page', 'Voir le magasin')])
@if($mapStores->isNotEmpty())
    <div class="store-map @if($mapStores->count() > 1) store-map--group @endif @if(s('design.map_theme', 'light') === 'dark') store-map--dark @endif"
         data-map
         data-markers='@json($mapMarkers)'
         data-labels='@json($mapLabels)'
         role="region" aria-label="{{ s('store.map_aria', 'Carte d\'accès aux magasins') }}">
        <a class="store-map-fallback" href="{{ $mapStores->first()->directionsUrl() }}" target="_blank" rel="noopener">{{ s('store.map_open', 'Ouvrir la carte et l\'itinéraire') }} →</a>
    </div>
    @once
        @push('scripts')<script src="/js/map.js?v={{ config('braises.asset_version') }}" defer></script>@endpush
    @endonce
@endif
