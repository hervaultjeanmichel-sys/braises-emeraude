@extends('layouts.front')
@section('content')
    <section class="sec wrap product">
        <div class="product-media">
            @foreach($product->images ?? [] as $img)
                <img src="{{ media($img) }}" alt="{{ $product->name }}" width="600" height="600" @unless($loop->first) loading="lazy" @endunless>
            @endforeach
        </div>
        <div class="product-text">
            @if($product->brand)<a class="eyebrow" href="{{ $product->brand->url() }}">{{ $product->brand->name }}</a>@endif
            <h1>{{ $product->h1 ?: $product->name }}</h1>
            @if($product->excerpt)<p class="lead">{{ $product->excerpt }}</p>@endif
            @if($product->specs)
                <dl class="specs">@foreach($product->specs as $spec)<dt>{{ $spec['label'] }}</dt><dd>{{ $spec['value'] }}</dd>@endforeach</dl>
            @endif
            <button class="btn btn-main" type="button" data-project-add="{{ $product->id }}" data-name="{{ $product->name }}" data-brand="{{ $product->brand?->name }}">{{ s('cta.add_project', 'Ajouter à mon projet') }}</button>
            @if($product->datasheet)<a class="btn btn-ghost" href="{{ media($product->datasheet) }}">{{ s('cta.datasheet', 'Fiche technique (PDF)') }}</a>@endif
        </div>
    </section>
    @if($product->content)<section class="sec wrap prose">{!! $product->content !!}</section>@endif
    @if($related->isNotEmpty())
        <section class="sec wrap"><h2>{{ s('product.related_title', 'Vous aimerez aussi') }}</h2>
            <div class="grid">@foreach($related as $p)@include('front.partials.product-card', ['product' => $p])@endforeach</div></section>
    @endif
@endsection
