{{-- Rendu des sections éditables (Builder Filament). Le gabarit est un moteur de rendu, jamais un contenu. --}}
@foreach($sections ?? [] as $block)
    @php($d = $block['data'] ?? [])
    @switch($block['type'] ?? '')
        @case('hero')
            <section class="hero"><div class="wrap">
                <div class="hero-text">
                    @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                    <h1>{{ $d['title'] ?? '' }} @if(!empty($d['title_accent']))<span class="loc">{{ $d['title_accent'] }}</span>@endif</h1>
                    @if(!empty($d['lead']))<p class="lead">{{ $d['lead'] }}</p>@endif
                    @if(!empty($d['cta_url']))<div class="ctas"><a class="btn btn-main" href="{{ $d['cta_url'] }}">{{ $d['cta_label'] ?? '' }}</a></div>@endif
                    @if(!empty($d['trust']))<div class="trust">@foreach($d['trust'] as $t)<div><b>{{ $t['value'] }}</b><span>{{ $t['label'] }}</span></div>@endforeach</div>@endif
                </div>
                @if(!empty($d['slides']))
                <div class="slider">@foreach($d['slides'] as $slide)
                    <figure class="slide @if($loop->first) on @endif"><img src="{{ media($slide['image']) }}" alt="{{ $slide['caption'] ?? '' }}" width="470" height="627" @unless($loop->first) loading="lazy" @endunless><figcaption>{{ $slide['caption'] ?? '' }}</figcaption></figure>
                @endforeach</div>
                @endif
            </div></section>
            @break
        @case('stores')
            <section class="sec groupe"><div class="wrap">
                <div class="sec-head">@if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif<h2>{{ $d['title'] ?? '' }}</h2>@if(!empty($d['text']))<p>{{ $d['text'] }}</p>@endif</div>
                <div class="stores">@foreach($stores as $store)
                    <a class="store" href="{{ $store->url() }}">
                        <div class="store-top"><span class="store-num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @if($store->is_new)<span class="badge new">{{ $store->badge_label ?: s('label.new', 'Nouveau') }}</span>@endif</div>
                        <h3><small>{{ $store->area_label }}</small>{{ $store->short_name }}</h3>
                        <span class="open-now @unless($store->isOpenNow()) closed @endunless">{{ $store->isOpenNow() ? s('label.open', 'Ouvert maintenant') : s('label.closed', 'Fermé') }}</span>
                        <p class="desc">{{ $store->intro }}</p>
                        <span class="go">{{ s('label.see_store', 'Voir le magasin') }} <span class="ar">→</span></span>
                    </a>
                @endforeach</div>
            </div></section>
            @break
        @case('categories')
            <section class="sec"><div class="wrap">
                <div class="sec-head">@if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif<h2>{{ $d['title'] ?? '' }}</h2>@if(!empty($d['text']))<p>{{ $d['text'] }}</p>@endif</div>
                <div class="univers">@foreach($categories as $cat)
                    <a class="uni" href="{{ $cat->url() }}"><h3>{{ $cat->name }}</h3><p>{{ $cat->intro }}</p></a>
                @endforeach</div>
            </div></section>
            @break
        @case('brands')
            <section class="sec"><div class="wrap">
                <div class="sec-head">@if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif<h2>{{ $d['title'] ?? '' }}</h2></div>
                <ul class="brands">@foreach($brands as $brand)<li><a href="{{ $brand->url() }}">{{ $brand->name }}</a></li>@endforeach</ul>
            </div></section>
            @break
        @case('text')
            <section class="sec wrap prose">
                @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                @if(!empty($d['title']))<h2>{{ $d['title'] }}</h2>@endif
                {!! $d['body'] ?? '' !!}
            </section>
            @break
        @case('steps')
            <section class="sec wrap">
                @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                <h2>{{ $d['title'] ?? '' }}</h2>
                <div class="steps">@foreach($d['items'] ?? [] as $item)<div><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] ?? '' }}</p></div>@endforeach</div>
            </section>
            @break
        @case('cta')
            <section class="sec cta wrap"><h2>{{ $d['title'] ?? '' }}</h2><p>{{ $d['text'] ?? '' }}</p>
                @if(!empty($d['button_url']))<a class="btn btn-main" href="{{ $d['button_url'] }}">{{ $d['button_label'] ?? '' }}</a>@endif</section>
            @break
    @endswitch
@endforeach
