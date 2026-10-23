@extends('layouts.front')
@section('content')
    <section class="page-hero" style="padding-block:5rem">
        <div class="wrap thanks">
            <span class="hand">{{ s('devis.thanks_hand', 'C\'est parti !') }}</span>
            <h1>{!! em(s('devis.thanks_h1', 'Merci, votre demande est *bien arrivée*')) !!}</h1>
            <p class="lead">{{ s('devis.thanks_text', 'Le magasin le plus proche de chez vous vous rappelle rapidement pour organiser la visite technique.') }}</p>
            <div class="ctas"><a class="btn btn-main" href="/" data-hover>{{ s('devis.thanks_home', 'Retour à l\'accueil') }}</a><a class="btn btn-ghost" href="/chauffage/" data-hover>{{ s('devis.thanks_catalogue', 'Continuer la visite') }}</a></div>
        </div>
    </section>
    @if(session('lead_type') === 'devis')<script>try{localStorage.removeItem('be_project')}catch(e){}</script>@endif
@endsection
