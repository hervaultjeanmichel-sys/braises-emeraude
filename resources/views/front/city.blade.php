@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $city->eyebrow ?: $city->store?->area_label, 'h1' => $city->h1 ?: $city->name, 'intro' => $city->intro,
        'actions' => '<a class="btn btn-main" href="/devis/'.($city->store ? '?magasin='.$city->store->id : '').'" data-hover>'.e(s('cta.quote', 'Devis gratuit')).'</a>'])
    <section class="content">
        <div class="wrap two-col">
            <div class="prose-page">{!! $city->content !!}</div>
            @if($city->store)
                <aside class="sticky" aria-label="{{ s('city.store_title', 'Votre magasin le plus proche') }}">
                    <p class="eyebrow">{{ s('city.store_title', 'Votre magasin le plus proche') }}</p>
                    <div class="stores">@include('front.partials.store-card', ['store' => $city->store, 'num' => $city->store->sort])</div>
                </aside>
            @endif
        </div>
    </section>
    @if(!empty($category) && $products->isNotEmpty())
        <section class="sec"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('city.products_eyebrow', 'En showroom') }}</span>
                <h2>{!! em(str_replace([':categorie', ':ville'], [mb_strtolower($category->name), $city->place()], s('city.products_title', 'Nos *:categorie* à :ville'))) !!}</h2></div>
            <div class="grid">@foreach($products as $product)@include('front.partials.product-card')@endforeach</div>
            <p style="text-align:center;margin-top:1.5rem"><a class="btn btn-ghost" href="{{ $category->url() }}">{{ s('city.products_all', 'Voir toute la gamme') }}</a></p>
        </div></section>
    @endif
    @if(true)
        <section class="sec bg-light"><div class="wrap two-col">
            @if($city->store?->brands->isNotEmpty())
                <div><h2>{!! em(str_replace(':ville', $city->place(), s('city.brands_title', 'Nos marques près de *:ville*'))) !!}</h2>
                    <ul class="chips">@foreach($city->store->brands as $brand)<li><a href="{{ $brand->url() }}">{{ $brand->name }}</a></li>@endforeach</ul></div>
            @endif
            @php($aids = \App\Models\AidTopic::published()->orderBy('sort')->limit(6)->get())
            @if($aids->isNotEmpty())
                <div><h2>{!! em(s('city.aids_title', 'Les *aides* pour votre poêle')) !!}</h2>
                    <ul class="chips">@foreach($aids as $aid)<li><a href="{{ $aid->url() }}">{{ $aid->title }}</a></li>@endforeach</ul></div>
            @endif
            @if($city->neighbours)
                <div><h2>{!! em(s('city.neighbours_title', 'Nous intervenons *aussi à*')) !!}</h2>
                    <ul class="chips">@foreach($city->neighbours as $n)<li><span>{{ $n }}</span></li>@endforeach</ul></div>
            @endif
        </div></section>
    @endif
    @if($city->faq)<section class="sec bg-mesh"><div class="wrap">@include('front.partials.faq', ['faq' => $city->faq])</div></section>@endif
    @include('front.partials.final-cta', ['url' => '/devis/'.($city->store ? '?magasin='.$city->store->id : '')])
@endsection
