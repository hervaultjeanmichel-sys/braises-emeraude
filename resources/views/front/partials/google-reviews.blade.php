{{-- Avis Google (Places API) : note officielle + avis choisis dans l'admin (Magasins › Avis Google).
     $grStores : magasins concernés ; $grTitle / $grEyebrow : textes du bloc ; $grLimit : nombre d'avis. --}}
@php($grSummary = \App\Support\GoogleReviews::summary($grStores ?? []))
@if($grSummary && $grSummary['rating'])
    @php($grReviews = array_slice($grSummary['reviews'], 0, (int) ($grLimit ?? 6)))
    @php($grPct = max(0, min(100, $grSummary['rating'] / 5 * 100)))
    <section class="sec greviews has-emblem emblem-left" id="avis" aria-labelledby="avis-title">
        <div class="wrap">
            <div class="sec-head">
                <span class="eyebrow">{{ $grEyebrow ?? s('reviews.eyebrow', 'Avis clients') }}</span>
                <h2 id="avis-title">{!! em($grTitle ?? s('reviews.title', 'Ils nous ont *fait confiance*')) !!}</h2>
            </div>
            <div class="gr-grid">
                <div class="gr-score">
                    <p class="gr-big">{{ number_format((float) $grSummary['rating'], 1, ',', '') }}<small>/5</small></p>
                    <p class="gr-stars" aria-label="{{ s('reviews.stars_aria', 'Note') }} {{ number_format((float) $grSummary['rating'], 1, ',', '') }} / 5"><span style="width:{{ $grPct }}%">★★★★★</span>★★★★★</p>
                    <p class="gr-count">{{ s('reviews.count_prefix', 'Note moyenne Google sur') }} <strong>{{ number_format($grSummary['count'], 0, ',', ' ') }} {{ s('reviews.count_suffix', 'avis') }}</strong></p>
                    @if(count($grSummary['stores']) > 1)
                        <ul class="gr-stores">
                            @foreach($grSummary['stores'] as $gs)
                                <li><a href="{{ $gs['mapsUri'] }}" target="_blank" rel="noopener">{{ $gs['store']->area_label ?: $gs['store']->short_name }}</a><span>{{ number_format((float) $gs['rating'], 1, ',', '') }} ★ · {{ $gs['count'] }} {{ s('reviews.count_suffix', 'avis') }}</span></li>
                            @endforeach
                        </ul>
                    @elseif(! empty($grSummary['stores'][0]['mapsUri']))
                        <p><a class="btn btn-ghost" href="{{ $grSummary['stores'][0]['mapsUri'] }}" target="_blank" rel="noopener">{{ s('reviews.see_all', 'Voir tous les avis sur Google Maps') }}</a></p>
                    @endif
                </div>
                <div class="gr-list">
                    @foreach($grReviews as $r)
                        <article class="gr-card">
                            <header>
                                @if($r['photo'])<img src="{{ $r['photo'] }}" alt="" width="40" height="40" loading="lazy" referrerpolicy="no-referrer">@endif
                                <div>
                                    @if($r['authorUri'])<a href="{{ $r['authorUri'] }}" target="_blank" rel="noopener nofollow"><strong>{{ $r['author'] }}</strong></a>@else<strong>{{ $r['author'] }}</strong>@endif
                                    <span class="gr-meta"><span class="gr-s" aria-label="{{ $r['rating'] }}/5">{{ str_repeat('★', $r['rating']) }}<i>{{ str_repeat('★', 5 - $r['rating']) }}</i></span> {{ $r['when'] }}@if(count($grSummary['stores']) > 1) · {{ $r['store'] }}@endif</span>
                                </div>
                            </header>
                            @if($r['text'])<p class="gr-text">{{ $r['text'] }}</p>@endif
                            <footer>
                                @if($r['visit'])<span>{{ s('reviews.visit', 'Visite') }} : {{ $r['visit'] }}</span>@endif
                                @if($r['translated'])<span>{{ s('reviews.translated', 'Traduit par Google') }}</span>@endif
                                @if($r['mapsUri'])<a href="{{ $r['mapsUri'] }}" target="_blank" rel="noopener">{{ s('reviews.on_google', 'Voir sur Google Maps') }}</a>@endif
                                @if($r['flagUri'])<a href="{{ $r['flagUri'] }}" target="_blank" rel="noopener nofollow">{{ s('reviews.flag', 'Signaler') }}</a>@endif
                            </footer>
                        </article>
                    @endforeach
                </div>
            </div>
            <p class="gr-notice">{{ s('reviews.notice', 'Avis publiés par des clients sur Google Maps. Ils ne sont pas vérifiés par Google ni par Braises Émeraude. Une sélection de ces avis est présentée ici ; la note moyenne et le nombre d’avis sont calculés par Google sur l’ensemble des avis de nos fiches.') }}</p>
        </div>
    </section>
@endif
