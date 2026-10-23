{{-- Gabarit public : SEO dynamique, en-tête Groupe + 3 magasins, pied de page avec les 3 adresses. --}}
@php($stores = \App\Models\Store::published()->orderBy('sort')->get())
@php($banner = \App\Models\EventBanner::current())
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo->fullTitle() }}</title>
    @if($seo->description)<meta name="description" content="{{ $seo->description }}">@endif
    <meta name="robots" content="{{ app()->environment('production') ? $seo->robots : 'noindex,nofollow' }}">
    <link rel="canonical" href="{{ $seo->absolute($seo->canonical) }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    @if($seo->description)<meta property="og:description" content="{{ $seo->description }}">@endif
    <meta property="og:url" content="{{ $seo->absolute($seo->canonical) }}">
    @if($seo->image)<meta property="og:image" content="{{ $seo->absolute(media($seo->image)) }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Caveat:wght@600&family=DM+Serif+Display:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/css/site.css?v={{ config('braises.asset_version') }}">
    @foreach($seo->allSchemas() as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endforeach
</head>
<body>
@if($banner)
    <div class="event-banner">
        <strong>{{ $banner->title }}</strong> {{ $banner->text }}
        @if($banner->cta_url)<a href="{{ $banner->cta_url }}">{{ $banner->cta_label }}</a>@endif
    </div>
@endif
<header class="top">
    <div class="wrap">
        <a class="logo" href="/"><img src="{{ media(s('group.logo', '/img/logo.webp')) }}" alt="{{ s('group.name', 'Braises Émeraude') }}" width="150" height="44"></a>
        <nav class="nav" aria-label="{{ s('nav.aria', 'Navigation principale') }}">
            @foreach($stores as $store)
                <a href="{{ $store->url() }}">{{ $store->short_name }}@if($store->is_new) <span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span>@endif</a>
            @endforeach
            <a href="/chauffage/">{{ s('nav.heating', 'Chauffage') }}</a>
            <a href="/cuisson/">{{ s('nav.cooking', 'Cuisson') }}</a>
            <a href="/aides-financement/">{{ s('nav.aids', 'Aides') }}</a>
            <a href="/nos-realisations/">{{ s('nav.realisations', 'Réalisations') }}</a>
        </nav>
        <a class="btn btn-main" href="/contact/">{{ s('cta.quote', 'Devis gratuit') }}</a>
    </div>
</header>

@include('front.partials.breadcrumb')

<main id="contenu">
    @yield('content')
</main>

<footer class="foot">
    <div class="wrap">
        <p class="slogan">{{ s('group.slogan', 'Le feu, c\'est la vie !') }}</p>
        <div class="foot-stores">
            @foreach($stores as $store)
                <address>
                    <a href="{{ $store->url() }}"><strong>{{ $store->name }}</strong></a><br>
                    @if($store->address){{ $store->address }}<br>@endif
                    {{ $store->postal_code }} {{ $store->city }}<br>
                    @if($store->phone)<a href="tel:{{ preg_replace('/\s+/', '', $store->phone) }}">{{ $store->phone }}</a>@endif
                </address>
            @endforeach
        </div>
        <p class="legal">{!! s('footer.legal', '© Braises Émeraude · <a href="/mentions-legales/">Mentions légales</a> · <a href="/politique-de-confidentialite/">Confidentialité</a>') !!}</p>
    </div>
</footer>
</body>
</html>
