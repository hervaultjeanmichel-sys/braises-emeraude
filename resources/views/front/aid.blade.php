@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => $aid->eyebrow, 'h1' => $aid->h1 ?: $aid->title, 'intro' => $aid->intro])
    @if($aid->verified_on)<p class="wrap updated">{{ s('aid.updated', 'Mis à jour le') }} {{ $aid->verified_on->locale('fr')->isoFormat('LL') }}</p>@endif
    @if($aid->content)<section class="sec wrap prose">{!! $aid->content !!}</section>@endif
    @if($aid->amounts)
        <section class="sec wrap"><table class="amounts">@foreach($aid->amounts as $a)<tr><th>{{ $a['label'] }}</th><td>{{ $a['amount'] ?? '' }}</td><td>{{ $a['condition'] ?? '' }}</td></tr>@endforeach</table></section>
    @endif
    @if($aid->financing_example)<section class="sec wrap"><h2>{{ s('aid.example_title', 'Exemple de financement') }}</h2><p>{!! nl2br(e($aid->financing_example)) !!}</p></section>@endif
    <section class="sec wrap cta"><a class="btn btn-main" href="/contact/">{{ s('cta.quote', 'Devis gratuit') }}</a></section>
    @include('front.partials.faq', ['faq' => $aid->faq])
@endsection
