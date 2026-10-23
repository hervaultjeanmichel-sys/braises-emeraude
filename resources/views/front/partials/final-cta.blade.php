{{-- Appel final commun aux sous-pages (fond braise). Textes : Réglages › Textes du site (groupe « devis »). --}}
<section class="sec bg-ember" aria-labelledby="final-cta">
    <div class="wrap final reveal">
        <span class="hand">{{ $hand ?? s('final.hand', 'On allume ?') }}</span>
        <h2 id="final-cta">{!! em($title ?? s('final.title', 'Parlons de votre futur *coin du feu*')) !!}</h2>
        <div class="ctas">
            <a class="btn btn-main" href="{{ $url ?? '/devis/' }}" data-hover>{{ s('final.cta', 'Demander mon devis gratuit') }}</a>
            <a class="btn btn-ghost" href="/#groupe" data-hover>{{ s('final.cta2', 'Passer en magasin') }}</a>
        </div>
    </div>
</section>
