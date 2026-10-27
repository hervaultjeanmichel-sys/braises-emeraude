{{-- Rendu des sections éditables (Builder Filament) — balisage repris de la maquette Contrastes.
     Le gabarit est un moteur de rendu : tous les textes viennent de $sections, des magasins, catégories et marques en base. --}}
@php($stores = $stores ?? \App\Models\Store::published()->orderBy('sort')->get())
@php($categories = $categories ?? \App\Models\ProductCategory::published()->whereNull('parent_id')->orderBy('sort')->get())
@php($brands = $brands ?? \App\Models\Brand::published()->orderBy('sort')->get())
@php($page = $page ?? null)
@php($romans = ['I', 'II', 'III', 'IV', 'V'])

@foreach($sections ?? [] as $block)
    @php($d = $block['data'] ?? [])
    @php($sid = 'sec-'.$loop->index)
    @switch($block['type'] ?? '')

        {{-- Bandeau cinéma --}}
        @case('saga')
            @php($eps = array_values($d['episodes'] ?? []))
            <section class="saga" id="saga" aria-labelledby="{{ $sid }}" aria-roledescription="carrousel">
                @php($bgVideo = $d['bg_video'] ?? null)
                @if($bgVideo)
                    <video class="saga-video" muted loop playsinline preload="none" @if(!empty($d['bg_poster'])) poster="{{ media($d['bg_poster']) }}" @endif data-src="{{ media($bgVideo) }}" data-type="{{ str_ends_with($bgVideo, '.webm') ? 'video/webm' : 'video/mp4' }}" aria-hidden="true"></video>
                @endif
                <div class="bar bar-top" aria-hidden="true"><span>{{ $d['kicker'] ?? '' }}</span><span id="ep-count">01 / {{ str_pad((string) max(1, count($eps)), 2, '0', STR_PAD_LEFT) }}</span></div>
                @foreach($eps as $i => $ep)
                    <article class="act @if($i === 0) on @endif" data-act="{{ $i }}">
                        @if($bgVideo)
                            {{-- décor commun : la vidéo de fond --}}
                        @elseif(($ep['scene'] ?? 'fire') === 'video' && !empty($ep['video']))
                            <video class="scene scene-video" @if($i === 0) autoplay @endif muted loop playsinline preload="{{ $i === 0 ? 'metadata' : 'none' }}" @if(!empty($ep['poster'])) poster="{{ media($ep['poster']) }}" @endif aria-hidden="true">
                                <source src="{{ media($ep['video']) }}" type="{{ str_ends_with($ep['video'], '.webm') ? 'video/webm' : 'video/mp4' }}">
                            </video>
                            @if(!empty($ep['temp']))<div class="temp" aria-hidden="true"><b>{{ $ep['temp'] }}</b><span>{{ s('saga.inside', 'dedans') }}</span></div>@endif
                        @else
                            <canvas class="scene" data-scene="{{ in_array($ep['scene'] ?? 'fire', ['fire', 'stove', 'forest'], true) ? $ep['scene'] : 'fire' }}" aria-hidden="true"></canvas>
                        @endif
                        <div class="act-inner wrap">
                            <div class="act-text">
                                <span class="ep">{{ $ep['label'] ?? s('saga.episode', 'Épisode').' '.($romans[$i] ?? $i + 1) }}</span>
                                <p class="act-title" data-split>{{ $ep['title'] ?? '' }}</p>
                                @if(!empty($ep['tagline']))<p class="act-tag">{{ $ep['tagline'] }}</p>@endif
                                @if(!empty($ep['rating']))<p class="rating">@include('front.partials.icon', ['name' => 'leaf']) {{ $ep['rating'] }}</p>@endif
                            </div>
                        </div>
                    </article>
                @endforeach
                <div class="bar bar-bottom">
                    <div class="wrap credits">
                        <div class="credits-text">
                            <h1 id="{{ $sid }}">{{ $d['h1'] ?? $page?->h1 }}</h1>
                            @if(!empty($d['billing']))<p class="billing">{!! preg_replace('/\*(.+?)\*/u', '<b>$1</b>', e($d['billing'])) !!}</p>@endif
                        </div>
                        <div class="credits-ui">
                            @if(!empty($d['cta_url']))<a class="btn btn-main" href="{{ $d['cta_url'] }}" data-hover>{{ $d['cta_label'] ?? '' }}</a>@endif
                            @if(count($eps) > 1)
                            <div class="saga-nav">
                                <button class="arrow" id="s-prev" type="button" aria-label="{{ s('saga.prev', 'Épisode précédent') }}" data-hover>@include('front.partials.icon', ['name' => 'prev'])</button>
                                <div class="reel" id="reel">@foreach($eps as $i => $ep)<button type="button" @if($i === 0) class="on" @endif aria-label="{{ $ep['label'] ?? s('saga.episode', 'Épisode').' '.($romans[$i] ?? $i + 1) }}"></button>@endforeach</div>
                                <button class="arrow" id="s-next" type="button" aria-label="{{ s('saga.next', 'Épisode suivant') }}" data-hover>@include('front.partials.icon', ['name' => 'next'])</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            @break

        {{-- Les magasins --}}
        @case('stores')
            <section class="sec @if($d['emblem'] ?? true) has-emblem @endif groupe bg-emerald" id="groupe" aria-labelledby="{{ $sid }}">
                <div class="wrap">
                    <div class="sec-head reveal">
                        @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                        <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                        @if(!empty($d['text']))<p>{{ $d['text'] }}</p>@endif
                    </div>
                    <div class="stores">
                        @foreach($stores as $store)
                            @include('front.partials.store-card', ['store' => $store, 'num' => $loop->iteration, 'linkLabel' => $d['link_label'] ?? null])
                        @endforeach
                    </div>
                    @if($d['show_map'] ?? true)
                        <div class="groupe-map reveal">@include('front.partials.store-map', ['mapStores' => $stores])</div>
                    @endif
                </div>
            </section>
            @break

        {{-- Univers produits --}}
        @case('categories')
            <section class="sec @if($d['emblem'] ?? false) has-emblem @endif {{ ($d['light'] ?? true) ? 'bg-light slant-top' : 'bg-mesh' }}" id="univers" aria-labelledby="{{ $sid }}">
                <div class="wrap">
                    <div class="sec-head reveal">
                        @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                        <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                    </div>
                    <div class="univers reveal">
                        @foreach($categories as $cat)
                            <a class="uni" href="{{ $cat->url() }}" data-hover>
                                <span class="ico">@include('front.partials.icon', ['name' => $cat->icon ?: 'bois'])</span>
                                <h3>{{ $cat->name }}</h3>
                                @if($cat->card_text ?: $cat->intro)<p>{{ $cat->card_text ?: \Illuminate\Support\Str::limit((string) $cat->intro, 140) }}</p>@endif
                                @if($line = $cat->brandsLine())<span class="brands">{{ $line }}</span>@endif
                                <span class="more">{{ $d['more_label'] ?? s('label.discover', 'Découvrir →') }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
            @break

        {{-- Signature de campagne --}}
        @case('quote')
            <section class="parallax" aria-label="{{ s('quote.aria', 'Signature de campagne') }}">
                <div class="bgimg" data-parallax style="background-image:url('{{ media($d['image'] ?? '/img/campagne-chien.webp') }}')" role="img" aria-label="{{ $d['image_alt'] ?? '' }}"></div>
                <div class="wrap">
                    <blockquote class="reveal">
                        <p class="big">{!! em($d['big'] ?? '') !!}</p>
                        @if(!empty($d['small']))<p class="small">{{ $d['small'] }}</p>@endif
                        @if(!empty($d['hand']))<span class="hand">{{ $d['hand'] }}</span>@include('front.partials.icon', ['name' => 'scribble'])@endif
                    </blockquote>
                </div>
            </section>
            @break

        {{-- Bandeau marques --}}
        @case('marquee')
            @if($brands->isNotEmpty())
            <div class="marquee bg-bar" aria-label="{{ $d['aria'] ?? s('brands.aria', 'Nos marques') }}">
                <div class="track">
                    @foreach([false, true] as $dup)
                        @foreach($brands as $brand)<a href="{{ $brand->url() }}" @if($dup) aria-hidden="true" tabindex="-1" @endif><span>{{ $brand->name }}</span></a><i>✦</i>@endforeach
                    @endforeach
                </div>
            </div>
            @endif
            @break

        {{-- Méthode --}}
        @case('steps')
            <section class="sec @if($d['emblem'] ?? false) has-emblem @endif bg-grain" id="methode" aria-labelledby="{{ $sid }}">
                <div class="wrap">
                    <div class="sec-head reveal">
                        @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                        <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                    </div>
                    <ol class="steps" style="list-style:none;margin:0;padding:0">
                        @foreach($d['items'] ?? [] as $item)
                            <li class="step reveal"><h3>{{ $item['title'] ?? '' }}</h3>@if(!empty($item['text']))<p>{{ $item['text'] }}</p>@endif</li>
                        @endforeach
                    </ol>
                </div>
            </section>
            @break

        {{-- Aides --}}
        @case('aids')
            <section class="sec bg-light slant-both" id="aides" aria-labelledby="{{ $sid }}">
                <div class="wrap">
                    <div class="aides reveal">
                        <div style="display:grid;gap:1.1rem">
                            @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                            <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                            @if(!empty($d['text']))<p>{{ $d['text'] }}</p>@endif
                            @if(!empty($d['cta_url']))<div class="ctas"><a class="btn btn-main" href="{{ $d['cta_url'] }}" data-hover>{{ $d['cta_label'] ?? '' }}</a></div>@endif
                        </div>
                        @if(!empty($d['bullets']))
                        <ul>@foreach($d['bullets'] as $b)<li>@include('front.partials.icon', ['name' => 'check'])<span>{{ $b }}</span></li>@endforeach</ul>
                        @endif
                    </div>
                </div>
            </section>
            @break

        {{-- Texte SEO + FAQ --}}
        @case('seo_faq')
            <section class="sec @if($d['emblem'] ?? true) has-emblem @endif bg-mesh" id="faq" aria-labelledby="{{ $sid }}">
                <div class="wrap seo-grid">
                    <div class="prose reveal">
                        @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                        <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                        {!! $d['body'] ?? '' !!}
                    </div>
                    @include('front.partials.faq', ['faq' => $page?->faq, 'title' => $d['faq_title'] ?? null])
                </div>
            </section>
            @break

        {{-- Appel final --}}
        @case('final')
            <section class="sec bg-ember" id="contact" aria-labelledby="{{ $sid }}">
                <div class="wrap final reveal">
                    @if(!empty($d['hand']))<span class="hand">{{ $d['hand'] }}</span>@endif
                    <h2 id="{{ $sid }}">{!! em($d['title'] ?? '') !!}</h2>
                    <div class="ctas">
                        @if(!empty($d['cta_url']))<a class="btn btn-main" href="{{ $d['cta_url'] }}" data-hover>{{ $d['cta_label'] ?? '' }}</a>@endif
                        @if(!empty($d['cta2_url']))<a class="btn btn-ghost" href="{{ $d['cta2_url'] }}" data-hover>{{ $d['cta2_label'] ?? '' }}</a>@endif
                    </div>
                </div>
            </section>
            @break

        {{-- Avis Google --}}
        @case('google_reviews')
            @include('front.partials.google-reviews', ['grStores' => $stores, 'grTitle' => $d['title'] ?? null, 'grEyebrow' => $d['eyebrow'] ?? null, 'grLimit' => $d['limit'] ?? 6])
            @break

        {{-- Texte libre --}}
        @case('text')
            <section class="sec @if($d['emblem'] ?? false) has-emblem @endif" aria-labelledby="{{ $sid }}">
                <div class="wrap two-col">
                    <div class="prose-page reveal">
                        @if(!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                        @if(!empty($d['title']))<h2 id="{{ $sid }}">{!! em($d['title']) !!}</h2>@endif
                        {!! $d['body'] ?? '' !!}
                    </div>
                    @if(!empty($d['image']))<img class="reveal" src="{{ media($d['image']) }}" alt="{{ $d['title'] ?? '' }}" width="600" height="450" loading="lazy" style="border-radius:22px">@endif
                </div>
            </section>
            @break

    @endswitch
@endforeach
