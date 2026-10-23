@extends('layouts.front')
@section('content')
    @php($hasH1 = collect($page->sections ?? [])->contains('type', 'saga'))
    @unless($hasH1)
        @include('front.partials.hero', ['eyebrow' => $page->eyebrow, 'h1' => $page->h1 ?: $page->title, 'intro' => $page->intro])
    @endunless
    @include('front.partials.sections', ['sections' => $page->sections])
    @if($page->content)<section class="content"><div class="wrap prose-page">{!! $page->content !!}</div></section>@endif
@endsection
