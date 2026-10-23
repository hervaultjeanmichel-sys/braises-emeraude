@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $brand->country, 'h1' => $brand->h1 ?: $brand->name, 'intro' => $brand->intro])
    @if($brand->content)<section class="sec wrap prose">{!! $brand->content !!}</section>@endif
    @if($products->isNotEmpty())
        <section class="sec wrap"><h2>{{ str_replace(':marque', $brand->name, s('brand.products_title', 'Les appareils :marque en catalogue')) }}</h2>
            <div class="grid">@foreach($products as $product)@include('front.partials.product-card')@endforeach</div></section>
    @endif
    @if($brand->stores->isNotEmpty())
        <section class="sec wrap"><h2>{{ str_replace(':marque', $brand->name, s('brand.stores_title', 'Voir :marque en fonctionnement')) }}</h2>
            <ul>@foreach($brand->stores as $store)<li><a href="{{ $store->url() }}">{{ $store->name }}</a></li>@endforeach</ul></section>
    @endif
@endsection
