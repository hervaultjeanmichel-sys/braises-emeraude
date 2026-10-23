{{-- Hero de sous-page : eyebrow + H1 unique + chapô, tous éditables. --}}
<section class="page-hero">
    <div class="page-hero-inner wrap">
        @if(!empty($eyebrow))<span class="eyebrow">{{ $eyebrow }}</span>@endif
        <h1>{{ $h1 }}</h1>
        @if(!empty($intro))<p class="lead">{{ $intro }}</p>@endif
    </div>
</section>
