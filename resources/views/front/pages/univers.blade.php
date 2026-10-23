{{-- Page univers (/chauffage-bois/, /cuisson/…) : texte de la page + cartes catégories + produits de la catégorie liée. --}}
@extends('layouts.front')
@php($linked = \App\Models\ProductCategory::published()->whereIn('path', [$page->slug, rtrim($page->slug, 's'), str_replace('granules', 'granule', $page->slug)])->first())
@php($cats = $linked ? $linked->children()->published()->get() : \App\Models\ProductCategory::published()->whereNull('parent_id')->orderBy('sort')->get())
@php($products = $linked ? \App\Models\Product::published()->whereIn('product_category_id', $linked->selfAndChildrenIds())->with('brand')->orderByDesc('is_featured')->orderBy('name')->limit(8)->get() : collect())
@section('content')
    @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro,
        'actions' => '<a class="btn btn-main" href="/devis/" data-hover>'.e(s('cta.quote', 'Devis gratuit')).'</a>'.($linked ? '<a class="btn btn-ghost" href="'.e($linked->url()).'" data-hover>'.e(s('cta.see_catalogue', 'Voir tout le catalogue')).'</a>' : '')])

    @if($cats->isNotEmpty())
        <section class="sec bg-light"><div class="wrap">
            <div class="univers reveal">
                @foreach($cats as $cat)
                    <a class="uni" href="{{ $cat->url() }}" data-hover>
                        <span class="ico">@include('front.partials.icon', ['name' => $cat->icon ?: ($linked->icon ?? 'bois')])</span>
                        <h3>{{ $cat->name }}</h3>
                        @if($cat->card_text ?: $cat->intro)<p>{{ $cat->card_text ?: \Illuminate\Support\Str::limit((string) $cat->intro, 140) }}</p>@endif
                        @if($line = $cat->brandsLine())<span class="brands">{{ $line }}</span>@endif
                        <span class="more">{{ s('label.discover', 'Découvrir →') }}</span>
                    </a>
                @endforeach
            </div>
        </div></section>
    @endif

    @if($page->content)
        <section class="content"><div class="wrap prose-page">{!! $page->content !!}</div></section>
    @endif

    @if($products->isNotEmpty())
        <section class="sec"><div class="wrap">
            <div class="sec-head"><span class="eyebrow">{{ s('univers.products_eyebrow', 'En exposition') }}</span><h2>{!! em(s('univers.products_title', 'Une sélection de nos *appareils*')) !!}</h2></div>
            <div class="grid">@foreach($products as $product)@include('front.partials.product-card')@endforeach</div>
        </div></section>
    @endif

    @include('front.partials.sections', ['sections' => $page->sections])
    @if(!empty($page->faq))<section class="sec bg-mesh"><div class="wrap">@include('front.partials.faq', ['faq' => $page->faq])</div></section>@endif
    @include('front.partials.final-cta')
@endsection
