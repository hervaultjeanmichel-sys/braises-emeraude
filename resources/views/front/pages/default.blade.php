@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro])
    @if($page->content)<section class="sec wrap prose">{!! $page->content !!}</section>@endif
    @include('front.partials.sections', ['sections' => $page->sections, 'stores' => \App\Models\Store::published()->orderBy('sort')->get(), 'categories' => \App\Models\ProductCategory::published()->whereNull('parent_id')->orderBy('sort')->get(), 'brands' => \App\Models\Brand::published()->orderBy('sort')->get()])
    @include('front.partials.faq', ['faq' => $page->faq])
@endsection
