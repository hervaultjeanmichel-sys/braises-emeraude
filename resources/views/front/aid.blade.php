@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $aid->eyebrow ?: s('aid.eyebrow', 'Aides & financement'), 'h1' => $aid->h1 ?: $aid->title, 'intro' => $aid->intro])
    <section class="content">
        <div class="wrap two-col">
            <div class="prose-page">
                @if($aid->verified_on)<p style="font-size:.85rem">{{ s('aid.updated', 'Mis à jour le') }} {{ $aid->verified_on->locale('fr')->isoFormat('LL') }}@if($aid->source_url) · <a href="{{ $aid->source_url }}" target="_blank" rel="noopener">{{ s('aid.source', 'source officielle') }}</a>@endif</p>@endif
                {!! $aid->content !!}
                @if($aid->financing_example)
                    <h2>{{ s('aid.example_title', 'Exemple de financement') }}</h2>
                    <p>{!! nl2br(e($aid->financing_example)) !!}</p>
                @endif
            </div>
            <aside class="card sticky">
                <h2>{{ s('aid.box_title', 'En résumé') }}</h2>
                @if($aid->amounts)
                    <table class="hours">@foreach($aid->amounts as $a)<tr><th scope="row" style="text-transform:none">{{ $a['label'] ?? '' }}@if(!empty($a['condition']))<br><small>{{ $a['condition'] }}</small>@endif</th><td><strong>{{ $a['amount'] ?? '' }}</strong></td></tr>@endforeach</table>
                @endif
                <p style="color:var(--muted);font-size:.9rem">{{ s('aid.box_text', 'Nous calculons les aides auxquelles vous avez droit lors du devis, gratuitement.') }}</p>
                <a class="btn btn-main" href="/devis/" data-hover>{{ s('cta.quote', 'Devis gratuit') }}</a>
            </aside>
        </div>
    </section>
    @if($aid->faq)<section class="sec bg-mesh"><div class="wrap">@include('front.partials.faq', ['faq' => $aid->faq])</div></section>@endif
    @include('front.partials.final-cta')
@endsection
