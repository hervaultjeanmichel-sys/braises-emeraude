{{-- Carte magasin (accueil, pages villes). Données 100 % issues de la fiche magasin. --}}
@php($open = $store->isOpenNow())
@php($hours = $store->hoursSummary())
<article class="store reveal" data-tilt>
    <div class="store-top">
        <span class="store-num" aria-hidden="true">{{ str_pad((string) ($num ?? $store->sort), 2, '0', STR_PAD_LEFT) }}</span>
        @if($store->is_new)<span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span>
        @elseif($store->region_label)<span class="badge">{{ $store->region_label }}</span>@endif
    </div>
    <div>
        <h3><small>{{ s('store.showroom', 'Showroom') }}</small>{{ $store->area_label ?: $store->short_name }}</h3>
        @if($store->card_text ?: $store->intro)<p class="desc">{{ $store->card_text ?: \Illuminate\Support\Str::limit((string) $store->intro, 160) }}</p>@endif
    </div>
    <dl>
        <div><dt>@include('front.partials.icon', ['name' => 'pin'])</dt><dd>{{ $store->addressLine() }}</dd></div>
        @if($store->phone)
            <div><dt>@include('front.partials.icon', ['name' => 'phone'])</dt><dd><a href="tel:{{ tel($store->phone) }}">{{ $store->phone }}</a></dd></div>
        @elseif($store->announcement)
            <div><dt>@include('front.partials.icon', ['name' => 'calendar'])</dt><dd>{{ $store->announcement }}</dd></div>
        @endif
        @if($hours)
            <div><dt>@include('front.partials.icon', ['name' => 'clock'])</dt><dd><span class="open-now @unless($open) closed @endunless" title="{{ $open ? s('label.open_title', 'Ouvert en ce moment') : s('label.closed_title', 'Fermé en ce moment') }}">{{ $hours[0] }}</span>@foreach(array_slice($hours, 1) as $h)<br><span class="muted">{{ $h }}</span>@endforeach</dd></div>
        @endif
    </dl>
    <a class="go" href="{{ $store->url() }}" data-hover>{{ str_replace(':magasin', $store->short_name, $linkLabel ?? s('store.link', 'Voir le magasin de :magasin')) }} <span class="ar">@include('front.partials.icon', ['name' => 'arrow'])</span></a>
</article>
