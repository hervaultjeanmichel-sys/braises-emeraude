@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $brand->country ?: s('brand.eyebrow', 'Marque'), 'h1' => $brand->h1 ?: $brand->name, 'intro' => $brand->intro])
    @if($brand->content)<section class="content"><div class="wrap prose-page">{!! $brand->content !!}</div></section>@endif
    @if($products->isNotEmpty())
        <section class="sec bg-mesh"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('brand.products_eyebrow', 'En catalogue') }}</span><h2>{!! em(str_replace(':marque', $brand->name, s('brand.products_title', 'Les appareils *:marque*'))) !!}</h2></div>
            <div class="grid">@foreach($products as $product)@include('front.partials.product-card')@endforeach</div>
        </div></section>
    @endif
    @if($brand->stores->isNotEmpty())
        <section class="sec groupe bg-emerald"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('brand.stores_eyebrow', 'En showroom') }}</span><h2>{!! em(str_replace(':marque', $brand->name, s('brand.stores_title', 'Voir *:marque* en fonctionnement'))) !!}</h2></div>
            <div class="stores">@foreach($brand->stores as $store)@include('front.partials.store-card', ['store' => $store, 'num' => $store->sort])@endforeach</div>
        </div></section>
    @endif
    @include('front.partials.final-cta')
@endsection
