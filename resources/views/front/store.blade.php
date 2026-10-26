@extends('layouts.front')
@php($open = $store->isOpenNow())
@php($today = now('Europe/Paris')->dayOfWeekIso)
@php($mapUrl = $store->latitude ? 'https://www.google.com/maps/search/?api=1&query='.$store->latitude.','.$store->longitude : 'https://www.google.com/maps/search/?api=1&query='.urlencode($store->name.' '.$store->addressLine()))
@section('content')
    @include('front.partials.hero', [
        'eyebrow' => $store->eyebrow ?: ($store->region_label ?: $store->area_label),
        'h1' => $store->h1 ?: $store->name,
        'intro' => $store->intro,
        'actions' => '<a class="btn btn-main" href="/devis/?magasin='.$store->id.'" data-hover>'.e(s('cta.quote', 'Devis gratuit')).'</a>'
            .($store->phone ? '<a class="btn btn-ghost" href="tel:'.e(tel($store->phone)).'" data-hover>'.e($store->phone).'</a>' : ''),
    ])

    <section class="content">
        <div class="wrap two-col">
            <div class="prose-page">
                @if($store->is_new)<p><span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span> @if($store->announcement)<strong>{{ $store->announcement }}</strong>@endif</p>@endif
                {!! $store->content !!}
            </div>
            <aside class="card sticky" aria-label="{{ s('store.infos', 'Infos pratiques') }}">
                <h2>{{ $store->area_label ?: $store->short_name }}</h2>
                <p class="open-now @unless($open) closed @endunless">{{ $open ? s('label.open', 'Ouvert maintenant') : s('label.closed', 'Fermé en ce moment') }}</p>
                <address style="font-style:normal">{{ $store->address }}@if($store->address)<br>@endif{{ $store->postal_code }} {{ $store->city }}</address>
                @if($store->phone)<p><a class="tel" href="tel:{{ tel($store->phone) }}">{{ $store->phone }}</a></p>@endif
                @if($store->email)<p><a href="mailto:{{ $store->email }}">{{ $store->email }}</a></p>@endif
                @if($store->opening_hours)
                    <table class="hours">
                        @foreach(collect($store->opening_hours)->groupBy(fn ($h) => (int) $h['day'])->sortKeys() as $day => $slots)
                            <tr @if($day === $today) class="today" @endif>
                                <th scope="row">{{ \Illuminate\Support\Carbon::now()->isoWeekday((int) $day)->locale('fr')->isoFormat('dddd') }}</th>
                                <td>{{ $slots->map(fn ($s) => $s['opens'].' – '.$s['closes'])->implode(' · ') }}</td>
                            </tr>
                        @endforeach
                    </table>
                @elseif($store->announcement)
                    <p>{{ $store->announcement }}</p>
                @endif
                <div class="ctas">
                    <a class="btn btn-ghost" href="{{ $mapUrl }}" target="_blank" rel="noopener">{{ s('store.directions', 'Itinéraire') }}</a>
                    @if($store->google_business_url)<a class="btn btn-ghost" href="{{ $store->google_business_url }}" target="_blank" rel="noopener">{{ s('store.reviews', 'Avis Google') }}</a>@endif
                </div>
            </aside>
        </div>
    </section>

    @include('front.partials.store-map')

    @if($store->photos)
        <section class="sec bg-mesh"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('store.photos_eyebrow', 'Le showroom') }}</span><h2>{!! em(s('store.photos_title', 'Des poêles *en fonctionnement*')) !!}</h2></div>
            <div class="store-photos">@foreach($store->photos as $photo)<img src="{{ media($photo) }}" alt="{{ $store->name }}" width="440" height="330" loading="lazy">@endforeach</div>
        </div></section>
    @endif

    @if($store->team)
        <section class="sec"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('store.team_eyebrow', 'L\'équipe') }}</span><h2>{!! em(s('store.team_title', 'Vos interlocuteurs *près de chez vous*')) !!}</h2></div>
            <div class="team">@foreach($store->team as $m)<figure>@if(!empty($m['photo']))<img src="{{ media($m['photo']) }}" alt="{{ $m['name'] }}" width="110" height="110" loading="lazy">@endif<figcaption><strong>{{ $m['name'] }}</strong><small>{{ $m['role'] ?? '' }}</small></figcaption></figure>@endforeach</div>
        </div></section>
    @endif

    @if($store->brands->isNotEmpty() || $cities->isNotEmpty() || $store->served_communes)
        <section class="sec bg-light"><div class="wrap two-col">
            @if($store->brands->isNotEmpty())
                <div><h2>{!! em(s('store.brands_title', 'Les marques *exposées*')) !!}</h2>
                    <ul class="chips">@foreach($store->brands as $brand)<li><a href="{{ $brand->url() }}">{{ $brand->name }}</a></li>@endforeach</ul></div>
            @endif
            @if($cities->isNotEmpty() || $store->served_communes)
                <div><h2>{!! em(s('store.communes_title', 'Communes *desservies*')) !!}</h2>
                    <ul class="chips">
                        @foreach($cities as $city)<li><a href="{{ $city->url() }}">{{ $city->name }}</a></li>@endforeach
                        @foreach(collect($store->served_communes ?? [])->diff($cities->pluck('name')) as $commune)<li><span>{{ $commune }}</span></li>@endforeach
                    </ul></div>
            @endif
        </div></section>
    @endif

    @include('front.partials.final-cta', ['url' => '/devis/?magasin='.$store->id])
@endsection
