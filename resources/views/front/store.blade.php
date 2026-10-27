@extends('layouts.front')
@php($open = $store->isOpenNow())
@php($today = now('Europe/Paris')->dayOfWeekIso)
@php($mapUrl = $store->directionsUrl())
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
                    @if(\App\Support\Booking::for($store)->isEnabled())<a class="btn btn-main" href="/rendez-vous/?magasin={{ $store->id }}" data-hover>{{ s('booking.cta', 'Prendre rendez-vous') }}</a>@endif
                    <a class="btn btn-ghost" href="{{ $mapUrl }}" target="_blank" rel="noopener">{{ s('store.directions', 'Itinéraire') }}</a>
                    @if($store->google_business_url)<a class="btn btn-ghost" href="{{ $store->google_business_url }}" target="_blank" rel="noopener">{{ s('store.reviews', 'Avis Google') }}</a>@endif
                </div>
            </aside>
        </div>
    </section>

    @if($store->latitude && $store->longitude)
        <section class="sec bg-mesh" id="carte" aria-labelledby="carte-title">
            <div class="wrap">
                <div class="sec-head">
                    <span class="eyebrow">{{ s('store.map_eyebrow', 'Nous trouver') }}</span>
                    <h2 id="carte-title">{!! em(s('store.map_title', 'Venez voir *le feu* en vrai')) !!}</h2>
                </div>
                @include('front.partials.store-map', ['mapStores' => [$store], 'currentPage' => true])
            </div>
        </section>
    @endif

    @php($gallery = collect($store->photos ?? [])->filter()->map(fn ($p) => media($p))->values())
    @if($gallery->isNotEmpty() || $store->video || $store->videoEmbedUrl())
        <section class="sec bg-mesh has-emblem" id="showroom" aria-labelledby="showroom-title">
            <div class="wrap">
                <div class="sec-head"><span class="eyebrow">{{ s('store.photos_eyebrow', 'Le showroom') }}</span><h2 id="showroom-title">{!! em(s('store.photos_title', 'Des poêles *en fonctionnement*')) !!}</h2></div>
                @if($store->video || $store->videoEmbedUrl())
                    <div class="store-video">
                        @if($store->video)
                            <video controls playsinline preload="none" @if($store->video_poster) poster="{{ media($store->video_poster) }}" @endif>
                                <source src="{{ media($store->video) }}" type="{{ str_ends_with($store->video, '.webm') ? 'video/webm' : 'video/mp4' }}">
                            </video>
                        @else
                            <button type="button" class="video-facade" data-embed="{{ $store->videoEmbedUrl() }}" data-title="{{ $store->name }}" aria-label="{{ s('store.video_play', 'Lire la vidéo du showroom') }}">
                                @if($thumb = $store->videoThumb())<img src="{{ $thumb }}" alt="" loading="lazy" width="1280" height="720">@endif
                                <span class="play" aria-hidden="true"></span>
                            </button>
                        @endif
                    </div>
                @endif
                @if($gallery->isNotEmpty())
                    <div class="store-gallery count-{{ min($gallery->count(), 10) }}">
                        @foreach($gallery->take(10) as $i => $photo)
                            <button type="button" class="sg-item" data-lightbox='@json($gallery)' data-index="{{ $i }}" data-title="{{ $store->name }}" aria-label="{{ s('store.photo_open', 'Agrandir la photo') }} {{ $i + 1 }}">
                                <img src="{{ $photo }}" alt="{{ s('store.photo_alt', 'Showroom') }} {{ $store->name }} — {{ $i + 1 }}" loading="lazy" decoding="async" width="800" height="600">
                            </button>
                        @endforeach
                    </div>
                    @push('scripts')<script src="/js/gallery.js?v={{ config('braises.asset_version') }}" defer></script>@endpush
                @endif
            </div>
        </section>
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

    @include('front.partials.google-reviews', ['grStores' => [$store], 'grTitle' => s('reviews.store_title', 'Ce que disent *nos clients*'), 'grLimit' => 5])

    @include('front.partials.final-cta', ['url' => '/devis/?magasin='.$store->id])
@endsection
