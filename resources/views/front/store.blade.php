@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $store->eyebrow ?: $store->area_label, 'h1' => $store->h1 ?: $store->name, 'intro' => $store->intro])
    <section class="sec wrap store-info">
        <address>
            {{ $store->address }}<br>{{ $store->postal_code }} {{ $store->city }}<br>
            @if($store->phone)<a href="tel:{{ preg_replace('/\s+/', '', $store->phone) }}">{{ $store->phone }}</a>@endif
        </address>
        <span class="open-now @unless($store->isOpenNow()) closed @endunless">{{ $store->isOpenNow() ? s('label.open', 'Ouvert maintenant') : s('label.closed', 'Fermé') }}</span>
        @if($store->opening_hours)
        <table class="hours">
            @foreach(collect($store->opening_hours)->groupBy('day')->sortKeys() as $day => $slots)
                <tr><th>{{ \Illuminate\Support\Carbon::now()->isoWeekday((int) $day)->locale('fr')->isoFormat('dddd') }}</th>
                    <td>{{ $slots->map(fn ($s) => $s['opens'].' – '.$s['closes'])->implode(' · ') }}</td></tr>
            @endforeach
        </table>
        @endif
    </section>
    @if($store->content)<section class="sec wrap prose">{!! $store->content !!}</section>@endif
    @if($store->brands->isNotEmpty())
        <section class="sec wrap"><h2>{{ s('store.brands_title', 'Les marques exposées dans notre showroom') }}</h2>
            <ul class="brands">@foreach($store->brands as $brand)<li><a href="{{ $brand->url() }}">{{ $brand->name }}</a></li>@endforeach</ul></section>
    @endif
    @if($cities->isNotEmpty() || $store->served_communes)
        <section class="sec wrap"><h2>{{ s('store.communes_title', 'Communes desservies') }}</h2>
            <ul class="communes">
                @foreach($cities as $city)<li><a href="{{ $city->url() }}">{{ $city->name }}</a></li>@endforeach
                @foreach($store->served_communes ?? [] as $commune)<li>{{ $commune }}</li>@endforeach
            </ul></section>
    @endif
@endsection
