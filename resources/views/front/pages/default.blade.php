@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro])
    @if($page->content)
        <section class="content"><div class="wrap prose-page">{!! $page->content !!}</div></section>
    @endif
    @include('front.partials.sections', ['sections' => $page->sections])
    @if(!empty($page->faq))
        <section class="sec bg-mesh"><div class="wrap">@include('front.partials.faq', ['faq' => $page->faq])</div></section>
    @endif
    @unless($page->template === 'legal')@include('front.partials.final-cta')@endunless
@endsection
