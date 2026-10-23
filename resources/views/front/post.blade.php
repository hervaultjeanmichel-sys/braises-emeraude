@extends('layouts.front')
@section('content')
    <article>
        @include('front.partials.hero', ['eyebrow' => $post->published_at->locale('fr')->isoFormat('LL'), 'h1' => $post->h1 ?: $post->title, 'intro' => $post->excerpt])
        @if($post->image)<img class="wrap" src="{{ media($post->image) }}" alt="{{ $post->title }}" width="1200" height="630">@endif
        <section class="sec wrap prose">{!! $post->content !!}</section>
    </article>
    @include('front.partials.faq', ['faq' => $post->faq])
@endsection
