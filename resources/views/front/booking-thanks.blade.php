@extends('layouts.front')
@section('content')
    <section class="page-hero" style="padding-block:5rem">
        <div class="wrap thanks">
            <span class="hand">{{ s('booking.thanks_hand', 'C’est noté !') }}</span>
            <h1>{!! em(s('booking.thanks_h1', 'Votre demande de rendez-vous est *bien arrivée*')) !!}</h1>
            @if($appointment)
                <p class="lead">{{ $appointment->type_label }}<br><strong>{{ $appointment->whenLabel() }}</strong><br>{{ $appointment->placeLabel() }}</p>
            @endif
            <p>{{ s('booking.thanks_text', 'Notre équipe vérifie le créneau et vous confirme le rendez-vous par e-mail ou par téléphone, en général sous 24 heures ouvrées. Un e-mail récapitulatif vient de vous être envoyé.') }}</p>
            <div class="ctas"><a class="btn btn-main" href="/" data-hover>{{ s('devis.thanks_home', 'Retour à l\'accueil') }}</a><a class="btn btn-ghost" href="/nos-realisations/" data-hover>{{ s('booking.thanks_more', 'Voir nos réalisations') }}</a></div>
        </div>
    </section>
@endsection
