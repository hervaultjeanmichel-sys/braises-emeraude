@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $category->parent?->name, 'h1' => $category->h1 ?: $category->name, 'intro' => $category->intro])
    @if($category->children->isNotEmpty())
        <nav class="wrap subcats">@foreach($category->children as $child)<a href="{{ $child->url() }}">{{ $child->name }}</a>@endforeach</nav>
    @endif
    <section class="sec wrap"><div class="grid">
        @foreach($products as $product)@include('front.partials.product-card')@endforeach
    </div>{{ $products->links() }}</section>
    @if($category->content)<section class="sec wrap prose">{!! $category->content !!}</section>@endif
@endsection
