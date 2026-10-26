@extends('layouts.front')
@php($images = $product->images ?? [])
@php($kw = $product->power_kw ? rtrim(rtrim(number_format($product->power_kw, 1, ',', ''), '0'), ',') : null)
@section('content')
    <div class="wrap">@include('front.partials.breadcrumb')</div>
    <section class="wrap product">
        <div class="gallery" data-gallery>
            <div class="main">
                @if($images)<img src="{{ media($images[0]) }}" alt="{{ $product->name }}" width="700" height="700" data-gallery-main fetchpriority="high">
                @else<div style="height:100%;display:grid;place-items:center;color:rgba(52,227,211,.35)"><span style="width:80px">@include('front.partials.icon', ['name' => $product->energy === 'granules' ? 'granules' : 'bois'])</span></div>@endif
            </div>
            @if(count($images) > 1)
                <div class="thumbs">@foreach($images as $img)<button type="button" class="@if($loop->first) on @endif" data-src="{{ media($img) }}" aria-label="{{ s('product.photo', 'Photo') }} {{ $loop->iteration }}"><img src="{{ media($img) }}" alt="" width="76" height="76" loading="lazy"></button>@endforeach</div>
            @endif
        </div>
        <div class="product-text">
            @if($product->brand)<a class="eyebrow" href="{{ $product->brand->url() }}">{{ $product->brand->name }}</a>@endif
            <h1>{{ $product->h1 ?: $product->name }}</h1>
            @if($product->excerpt)<p class="lead" style="color:var(--muted);font-size:1.08rem">{{ $product->excerpt }}</p>@endif
            @if($kw || $product->surface_max_m2 || $product->energy)
                <div class="keyfacts">
                    @if($kw)<div><b>{{ $kw }}</b><span>{{ s('product.kw', 'kW de puissance') }}</span></div>@endif
                    @if($product->surface_max_m2)<div><b>{{ $product->surface_min_m2 ? $product->surface_min_m2.'–' : '' }}{{ $product->surface_max_m2 }}</b><span>{{ s('product.m2', 'm² chauffés') }}</span></div>@endif
                    @if($product->energy)<div><b>{{ s('energy.'.$product->energy, ucfirst($product->energy)) }}</b><span>{{ s('product.energy', 'énergie') }}</span></div>@endif
                </div>
            @endif
            <div class="ctas">
                <button class="btn btn-main" type="button" data-hover data-project-add="{{ $product->id }}" data-name="{{ $product->name }}" data-brand="{{ $product->brand?->name }}" data-url="{{ $product->url() }}" data-img="{{ media($images[0] ?? null) }}"
                    data-label-on="{{ s('cta.in_project', '✓ Dans mon projet') }}">{{ s('cta.add_project', 'Ajouter à mon projet') }}</button>
                <a class="btn btn-ghost" href="/devis/?produit={{ $product->id }}" data-hover>{{ s('cta.quote_product', 'Devis pour cet appareil') }}</a>
            </div>
            @if($product->specs)
                <dl class="specs">@foreach($product->specs as $spec)<dt>{{ $spec['label'] ?? '' }}</dt><dd>{{ $spec['value'] ?? '' }}</dd>@endforeach</dl>
            @endif
            @if($product->datasheet)<a href="{{ media($product->datasheet) }}" style="color:var(--emerald);font-weight:600" target="_blank" rel="noopener">{{ s('cta.datasheet', 'Télécharger la fiche technique (PDF)') }}</a>@endif
        </div>
    </section>
    @if($product->content)
        <section class="sec bg-mesh"><div class="wrap prose-page">{!! $product->content !!}</div></section>
    @endif
    @if($related->isNotEmpty())
        <section class="sec"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('product.related_eyebrow', 'À découvrir aussi') }}</span><h2>{!! em(s('product.related_title', 'Dans la *même famille*')) !!}</h2></div>
            <div class="grid">@foreach($related as $product)@include('front.partials.product-card')@endforeach</div>
        </div></section>
    @endif
    @include('front.partials.final-cta')
@endsection
