@extends('layouts.front')
@section('content')
    <article>
        @include('front.partials.hero', ['eyebrow' => $post->published_at->locale('fr')->isoFormat('LL'), 'h1' => $post->h1 ?: $post->title, 'intro' => $post->excerpt])
        @if($post->image)<div class="wrap"><img src="{{ media($post->image) }}" alt="{{ $post->title }}" width="1200" height="630" style="border-radius:24px;width:100%;aspect-ratio:1200/630;object-fit:cover"></div>@endif
        <section class="content"><div class="wrap two-col">
            <div class="prose-page">{!! $post->content !!}</div>
            @if($post->store)
                <aside class="sticky"><p class="eyebrow">{{ s('post.store_title', 'Votre magasin') }}</p><div class="stores">@include('front.partials.store-card', ['store' => $post->store, 'num' => $post->store->sort])</div></aside>
            @endif
        </div></section>
    </article>
    @if($post->faq)<section class="sec bg-mesh"><div class="wrap">@include('front.partials.faq', ['faq' => $post->faq])</div></section>@endif
    @include('front.partials.final-cta')
@endsection
