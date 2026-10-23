{{-- En-tête de sous-page : fil d'Ariane + eyebrow + H1 unique + chapô (tous éditables). --}}
<section class="page-hero">
    <div class="wrap">
        @include('front.partials.breadcrumb')
        <div class="page-hero-inner">
            @if(!empty($eyebrow))<span class="eyebrow">{{ $eyebrow }}</span>@endif
            <h1>{!! em($h1) !!}</h1>
            @if(!empty($intro))<p class="lead">{{ $intro }}</p>@endif
            @isset($actions)<div class="ctas">{!! $actions !!}</div>@endisset
        </div>
    </div>
</section>
