{{-- Décompte avant l'ouverture de Rennes, au-dessus de l'en-tête (JM, 06/11/2026).
     Réglages › Textes du site, groupe « countdown » : date (AAAA-MM-JJ HH:MM, heure de Paris), texte, lien, affichage.
     Disparaît tout seul une fois la date passée (côté serveur et côté navigateur). --}}
@php
    $cdAt = null;
    if (s('countdown.enabled', '1') === '1') {
        try {
            $cdAt = \Illuminate\Support\Carbon::parse((string) s('countdown.date', '2026-11-17 09:00'), 'Europe/Paris');
        } catch (\Throwable $e) {
            $cdAt = null;
        }
    }
    $cdLeft = $cdAt ? now('Europe/Paris')->diffInMinutes($cdAt, false) : 0;
@endphp
@if($cdAt && $cdLeft > 0)
    <div class="countdown" role="note" data-countdown="{{ $cdAt->toIso8601String() }}">
        <div class="wrap countdown-in">
            <span class="cd-text">{!! em(s('countdown.text', 'Ouverture de notre magasin de *Rennes* le 17 novembre 2026')) !!}</span>
            <span class="cd-timer">
                <span class="cd-u"><b data-cd="d">{{ intdiv((int) $cdLeft, 1440) }}</b> {{ s('countdown.unit_days', 'jours') }}</span>
                <span class="cd-u"><b data-cd="h">{{ intdiv((int) $cdLeft % 1440, 60) }}</b> {{ s('countdown.unit_hours', 'h') }}</span>
                <span class="cd-u"><b data-cd="m">{{ (int) $cdLeft % 60 }}</b> {{ s('countdown.unit_minutes', 'min') }}</span>
            </span>
            @if(s('countdown.link_url'))
                <a class="cd-link" href="{{ s('countdown.link_url') }}">{{ s('countdown.link_label', 'Découvrir le magasin') }}</a>
            @endif
        </div>
    </div>
@endif
