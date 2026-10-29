{{-- Gabarit public — thème Contrastes. SEO dynamique, en-tête Groupe, pied de page avec les 3 magasins. Aucun texte en dur : s() lit Réglages › Textes du site. --}}
@php($layoutStores = \App\Models\Store::published()->orderBy('sort')->get())
@php($banner = \App\Models\EventBanner::current())
@php($v = config('braises.asset_version'))
<!doctype html>
<html lang="fr" class="no-js @if(s('design.eyebrow_emblem', '1') === '1') emblem-eyebrow @endif">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $seo->fullTitle() }}</title>
    @if($seo->description)<meta name="description" content="{{ $seo->description }}">@endif
    <meta name="robots" content="{{ app()->environment('production') ? $seo->robots : 'noindex,nofollow' }}">
    <link rel="canonical" href="{{ $seo->absolute($seo->canonical) }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="{{ s('group.name', 'Braises Émeraude') }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    @if($seo->description)<meta property="og:description" content="{{ $seo->description }}">@endif
    <meta property="og:url" content="{{ $seo->absolute($seo->canonical) }}">
    <meta property="og:image" content="{{ $seo->absolute(media($seo->image ?: (s('seo.og_image') ?: '/img/campagne-chien.webp'))) }}">
    <meta name="theme-color" content="#060908">
    <link rel="icon" href="{{ s('group.favicon', '/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Caveat:wght@600&family=DM+Serif+Display:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="/css/site.css?v={{ $v }}">
    <script>document.documentElement.classList.replace('no-js','js')</script>
    @foreach($seo->allSchemas() as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endforeach
    @stack('head')
</head>
<body>
<a class="skip" href="#contenu">{{ s('a11y.skip', 'Aller au contenu') }}</a>

@if($banner)
    <div class="event-banner" role="note">
        <strong>{{ $banner->title }}</strong> {{ $banner->text }}
        @if($banner->cta_url)<a href="{{ $banner->cta_url }}">{{ $banner->cta_label }}</a>@endif
    </div>
@endif

<header class="top" id="top">
    <div class="wrap">
        <a class="logo" href="/" aria-label="{{ s('group.name', 'Braises Émeraude') }} — {{ s('nav.home', 'accueil') }}"><img src="{{ media(s('group.logo', '/img/logo.webp')) }}" alt="{{ s('group.logo_alt', 'Braises Émeraude, poêles à bois et à granulés') }}" width="324" height="130"></a>
        <nav class="nav" aria-label="{{ s('nav.aria', 'Navigation principale') }}">
            @foreach(menu('menu.main', "Nos magasins|/#groupe\nChauffage|/chauffage/\nCuisson|/cuisson/\nAides & financement|/aides-financement/\nRéalisations|/nos-realisations/\nContact|/contact/") as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
            @foreach($layoutStores->where('is_new', true) as $store)
                <a href="{{ $store->url() }}">{{ $store->short_name }} <span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span></a>
            @endforeach
        </nav>
        <div class="top-actions">
            <a class="project-link" href="/mon-projet/" aria-label="{{ s('project.title', 'Mon projet') }}" data-hover>
                @include('front.partials.icon', ['name' => 'project'])
                <span class="project-count" data-project-count hidden>0</span>
            </a>
            <a class="btn btn-main" href="/devis/" data-hover>{{ s('cta.quote', 'Devis gratuit') }}</a>
            <button class="burger" type="button" aria-expanded="false" aria-controls="mnav" aria-label="{{ s('nav.open', 'Ouvrir le menu') }}" data-burger>@include('front.partials.icon', ['name' => 'menu'])</button>
        </div>
    </div>
    <nav class="mnav" id="mnav" aria-label="{{ s('nav.aria_mobile', 'Menu mobile') }}">
        @foreach($layoutStores as $store)<a href="{{ $store->url() }}">{{ $store->area_label ?: $store->short_name }}@if($store->is_new) <span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span>@endif</a>@endforeach
        @foreach(menu('menu.main', "Chauffage|/chauffage/\nCuisson|/cuisson/\nAides & financement|/aides-financement/\nContact|/contact/") as $item)
            @unless(str_starts_with($item['url'], '/#'))<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@endunless
        @endforeach
        <a href="/devis/">{{ s('cta.quote', 'Devis gratuit') }}</a>
    </nav>
</header>

<main id="contenu">
    @yield('content')
</main>

<footer>
    <div class="wrap">
        <div class="foot">
            <div style="display:grid;gap:1rem;align-content:start">
                <img src="{{ media(s('group.logo', '/img/logo.webp')) }}" alt="{{ s('group.name', 'Braises Émeraude') }}" width="324" height="130" style="height:48px;width:auto" loading="lazy">
                <p>{{ s('footer.text', 'Vente, installation et entretien de poêles à bois, à granulés et mixtes en Ille-et-Vilaine.') }}</p>
            </div>
            @foreach($layoutStores as $store)
                <div>
                    <h4><a href="{{ $store->url() }}">{{ $store->area_label ?: $store->short_name }}</a></h4>
                    <address>
                        {{ $store->addressLine() }}
                        @if($store->phone)<a class="tel" href="tel:{{ tel($store->phone) }}">{{ $store->phone }}</a>@elseif($store->announcement)<span>{{ $store->announcement }}</span>@endif
                    </address>
                </div>
            @endforeach
        </div>
        <div class="legal">
            <span>{{ s('footer.copyright', '© '.date('Y').' Braises Émeraude · La Soleillade') }}</span>
            <nav aria-label="{{ s('footer.legal_aria', 'Liens légaux') }}">
                @foreach(menu('menu.legal', "Mentions légales|/mentions-legales/\nConfidentialité|/politique-de-confidentialite/\nPlan du site|/sitemap.xml") as $item)
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </div>
    </div>
</footer>

<div class="cur" aria-hidden="true"></div>
<div class="flame" aria-hidden="true">@include('front.partials.icon', ['name' => 'flame'])</div>

@php($fx = \App\Models\SeasonalEffect::current())
@if($fx && ($fx->scope === 'all' || request()->is('/')))
    @php($fxConfig = ['id' => $fx->id.'-'.$fx->updated_at?->timestamp, 'type' => $fx->effect, 'intensity' => $fx->intensity, 'labelOff' => '✕ '.$fx->name, 'labelOn' => '✨ '.$fx->name])
    <script>window.BE_EFFECT=@json($fxConfig);</script>
    <script src="/js/effects.js?v={{ $v }}" defer></script>
@endif
@php($promo = \App\Models\PromoSlide::current(request()->is('/')))
@if($promo)
    @include('front.partials.promo-slide', ['promo' => $promo])
    <script src="/js/promo.js?v={{ $v }}" defer></script>
@endif
<script src="/js/contrastes.js?v={{ $v }}" defer></script>
<script src="/js/site.js?v={{ $v }}" defer></script>
@stack('scripts')
</body>
</html>
