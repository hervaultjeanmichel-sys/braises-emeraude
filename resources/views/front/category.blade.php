@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $category->parent?->name ?: s('catalogue.eyebrow', 'Catalogue'), 'h1' => $category->h1 ?: $category->name, 'intro' => $category->intro])
    <section class="content" style="padding-top:1rem">
        <div class="wrap">
            @if($category->children->isNotEmpty())
                <ul class="chips" style="margin-bottom:1.5rem">
                    @foreach($category->children as $child)<li><a href="{{ $child->url() }}">{{ $child->name }}</a></li>@endforeach
                </ul>
            @endif
            @if($brands->count() > 1 || $energies->count() > 1)
                <div class="filters" role="search" aria-label="{{ s('catalogue.filters', 'Filtrer les appareils') }}">
                    @if($brands->count() > 1)
                        <div><span class="label">{{ s('catalogue.brand', 'Marque') }}</span>
                            <ul class="chips" style="display:inline-flex">
                                <li><a class="@if($activeBrand === '') on @endif" href="{{ $category->url() }}{{ $activeEnergy ? '?energie='.$activeEnergy : '' }}">{{ s('catalogue.all', 'Toutes') }}</a></li>
                                @foreach($brands as $b)<li><a class="@if($activeBrand === $b->slug) on @endif" href="{{ $category->url() }}?{{ http_build_query(array_filter(['marque' => $b->slug, 'energie' => $activeEnergy])) }}" rel="nofollow">{{ $b->name }}</a></li>@endforeach
                            </ul></div>
                    @endif
                    @if($energies->count() > 1)
                        <div><span class="label">{{ s('catalogue.energy', 'Énergie') }}</span>
                            <ul class="chips" style="display:inline-flex">
                                @foreach($energies as $en)<li><a class="@if($activeEnergy === $en) on @endif" href="{{ $category->url() }}?{{ http_build_query(array_filter(['marque' => $activeBrand, 'energie' => $en])) }}" rel="nofollow">{{ s('energy.'.$en, ucfirst($en)) }}</a></li>@endforeach
                            </ul></div>
                    @endif
                </div>
            @endif
            @if($products->isEmpty())
                <p class="empty">{{ s('catalogue.empty', 'Aucun appareil ne correspond à ces critères pour le moment.') }}</p>
            @else
                <p class="hint" style="color:var(--muted);margin-bottom:1rem">{{ $products->total() }} {{ s('catalogue.count', 'appareil(s)') }}</p>
                <div class="grid">@foreach($products as $product)@include('front.partials.product-card')@endforeach</div>
                {{ $products->onEachSide(1)->links('front.partials.pagination') }}
            @endif
        </div>
    </section>
    @if($category->content)<section class="sec bg-mesh"><div class="wrap prose-page">{!! $category->content !!}</div></section>@endif
    @include('front.partials.final-cta')
@endsection
