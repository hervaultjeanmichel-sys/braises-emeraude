{{-- Volet promo glissant depuis la gauche (Contenus › Volets promo). Tout le contenu vient de la base. --}}
@php($promoCenter = ($promo->position ?? 'center') !== 'bottom-left')
@if($promoCenter)<div class="promo-backdrop" id="promo-backdrop" hidden></div>@endif
<aside class="promo-slide {{ $promoCenter ? 'pos-center' : 'pos-corner' }} {{ $promo->isLightBackground() ? 'is-light' : 'is-dark' }}" id="promo-slide" style="--promo-bg:{{ $promo->bg_color }}" role="dialog" aria-modal="false" @if($promo->title) aria-labelledby="promo-title" @else aria-label="{{ $promo->image_alt ?: $promo->name }}" @endif data-key="{{ $promo->memoKey() }}" data-delay="{{ (int) $promo->delay }}" data-frequency="{{ $promo->frequency }}" hidden>
    <button class="promo-close" type="button" aria-label="{{ s('promo.close', 'Fermer') }}">✕</button>
    @if($promo->image)
        @php($promoImg = $promo->cta_url ? '<a href="'.e($promo->cta_url).'" class="promo-img" tabindex="-1">' : '<div class="promo-img">')
        {!! $promoImg !!}<img src="{{ media($promo->image) }}" alt="{{ $promo->image_alt ?? '' }}" loading="lazy" decoding="async">{!! $promo->cta_url ? '</a>' : '</div>' !!}
    @endif
    @if($promo->eyebrow || $promo->title || $promo->text || $promo->cta_url)
    <div class="promo-body">
        @if($promo->eyebrow)<span class="promo-eyebrow">{{ $promo->eyebrow }}</span>@endif
        @if($promo->title)<p class="promo-title" id="promo-title">{{ $promo->title }}</p>@endif
        @if($promo->text)<p class="promo-text">{{ $promo->text }}</p>@endif
        @if($promo->cta_url && $promo->cta_label)<a class="btn promo-cta" href="{{ $promo->cta_url }}">{{ $promo->cta_label }}</a>@endif
    </div>
    @endif
</aside>
