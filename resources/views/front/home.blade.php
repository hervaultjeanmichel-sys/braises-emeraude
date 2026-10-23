@extends('layouts.front')
@section('content')
    @php($hasHero = collect($page->sections ?? [])->contains('type', 'hero'))
    @unless($hasHero)
        @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro])
    @endunless
    @include('front.partials.sections', ['sections' => $page->sections])
    @if($page->content)<section class="sec wrap prose">{!! $page->content !!}</section>@endif
    @include('front.partials.faq', ['faq' => $page->faq])
@endsection
