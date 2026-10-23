@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $city->eyebrow, 'h1' => $city->h1 ?: $city->name, 'intro' => $city->intro])
    @if($city->content)<section class="sec wrap prose">{!! $city->content !!}</section>@endif
    @if($city->store)
        <section class="sec wrap"><h2>{{ s('city.store_title', 'Votre magasin le plus proche') }}</h2>
            <p><a href="{{ $city->store->url() }}">{{ $city->store->name }}</a> — {{ $city->store->postal_code }} {{ $city->store->city }}</p>
            @if($city->store->brands->isNotEmpty())
                <ul class="brands">@foreach($city->store->brands as $brand)<li><a href="{{ $brand->url() }}">{{ $brand->name }}</a></li>@endforeach</ul>
            @endif
        </section>
    @endif
    @if($city->neighbours)
        <section class="sec wrap"><h2>{{ s('city.neighbours_title', 'Nous intervenons aussi à') }}</h2><p>{{ implode(' · ', $city->neighbours) }}</p></section>
    @endif
    @include('front.partials.faq', ['faq' => $city->faq])
@endsection
