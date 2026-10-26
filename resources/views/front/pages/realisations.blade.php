{{-- Page Réalisations : chantiers en photos + avis clients (affichés seulement avec l'accord du client).
     Les chantiers se gèrent dans Admin › Contenus › Réalisations. --}}
@extends('layouts.front')
@php($reals = \App\Models\Realisation::published()->with(['store', 'product'])->orderByDesc('is_featured')->orderBy('sort')->orderByDesc('installed_on')->get())
@php($realStores = $reals->pluck('store')->filter()->unique('id')->sortBy('sort'))
@php($realEnergies = $reals->pluck('energy')->filter()->unique())
@php($reviews = $reals->filter->hasPublicReview())
@php($rated = $reviews->whereNotNull('review_rating'))
@php($avg = $rated->isNotEmpty() ? round((float) $rated->avg('review_rating'), 1) : null)
@section('content')
    @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro,
        'actions' => '<a class="btn btn-main" href="/devis/" data-hover>'.e(s('cta.quote', 'Devis gratuit')).'</a>'])

    @if($reals->isNotEmpty())
        <section class="sec" id="chantiers" aria-labelledby="chantiers-title">
            <div class="wrap">
                <div class="sec-head">
                    <span class="eyebrow">{{ s('real.eyebrow', 'Nos chantiers') }}</span>
                    <h2 id="chantiers-title">{!! em(s('real.title', 'Ils se chauffent *avec nous*')) !!}</h2>
                    @if($reviews->isNotEmpty())
                        <p>{{ trans_choice(s('real.reviews_count', ':count avis client|:count avis clients'), $reviews->count(), ['count' => $reviews->count()]) }}@if($avg) · {{ s('real.avg', 'note moyenne') }} {{ str_replace('.', ',', (string) $avg) }}/5@endif</p>
                    @endif
                </div>

                @if($realStores->count() > 1 || $realEnergies->count() > 1)
                    <div class="real-filters" role="group" aria-label="{{ s('real.filter_aria', 'Filtrer les réalisations') }}" data-real-filters>
                        <button type="button" class="on" data-filter="*" aria-pressed="true">{{ s('real.all', 'Toutes') }}</button>
                        @foreach($realStores as $st)<button type="button" data-filter="store-{{ $st->id }}" aria-pressed="false">{{ $st->short_name }}</button>@endforeach
                        @foreach($realEnergies as $en)<button type="button" data-filter="energy-{{ $en }}" aria-pressed="false">{{ \App\Models\Realisation::ENERGIES[$en] ?? $en }}</button>@endforeach
                    </div>
                @endif

                <div class="reals">
                    @foreach($reals as $r)
                        @php($photos = collect($r->photos ?? [])->map(fn ($p) => media($p))->values())
                        <article class="real reveal @if($r->is_featured) is-featured @endif" data-tags="store-{{ $r->store_id }} energy-{{ $r->energy }}">
                            @if($photos->isNotEmpty())
                                <button type="button" class="real-photo" data-lightbox='@json($photos)' data-title="{{ $r->title }}" aria-label="{{ s('real.see_photos', 'Voir les photos') }} : {{ $r->title }}">
                                    <img src="{{ $photos[0] }}" alt="{{ $r->title }}" width="800" height="600" loading="lazy" decoding="async">
                                    @if($photos->count() > 1)<span class="real-count">{{ $photos->count() }} {{ s('real.photos', 'photos') }}</span>@endif
                                </button>
                            @endif
                            <div class="real-body">
                                <p class="real-meta">{{ collect([$r->city, $r->store?->short_name ? s('real.store_prefix', 'magasin de').' '.$r->store->short_name : null])->filter()->implode(' · ') }}</p>
                                <h3>{{ $r->title }}</h3>
                                @if($r->product)<p class="real-product"><a href="{{ $r->product->url() }}">{{ $r->product->name }}</a></p>@endif
                                @if($r->hasPublicReview())
                                    <blockquote class="real-review">
                                        @if($r->review_rating)<p class="stars" aria-label="{{ s('real.rating', 'Note') }} : {{ $r->review_rating }}/5"><span aria-hidden="true">{{ str_repeat('★', (int) $r->review_rating) }}<i>{{ str_repeat('★', 5 - (int) $r->review_rating) }}</i></span></p>@endif
                                        <p>« {{ $r->review_text }} »</p>
                                        <footer>{{ $r->review_author }}@if($r->review_source) · {{ \App\Models\Realisation::SOURCES[$r->review_source] ?? $r->review_source }}@endif @if($r->review_date) · {{ $r->review_date->locale('fr')->isoFormat('MMMM YYYY') }}@endif</footer>
                                    </blockquote>
                                @elseif($r->description)
                                    <p>{{ $r->description }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <dialog class="lightbox" data-lightbox-dialog aria-label="{{ s('real.gallery', 'Galerie photos') }}">
            <button type="button" class="lb-close" data-lb-close aria-label="{{ s('real.close', 'Fermer') }}">×</button>
            <button type="button" class="lb-nav lb-prev" data-lb-prev aria-label="{{ s('real.prev', 'Photo précédente') }}">‹</button>
            <figure><img src="" alt="" data-lb-img><figcaption data-lb-caption></figcaption></figure>
            <button type="button" class="lb-nav lb-next" data-lb-next aria-label="{{ s('real.next', 'Photo suivante') }}">›</button>
        </dialog>
        @push('scripts')<script src="/js/realisations.js?v={{ config('braises.asset_version') }}" defer></script>@endpush
    @endif

    @if($page->content)
        <section class="content"><div class="wrap prose-page">{!! $page->content !!}</div></section>
    @endif
    @include('front.partials.sections', ['sections' => $page->sections])
    @include('front.partials.final-cta')
@endsection
