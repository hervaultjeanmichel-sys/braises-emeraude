{{-- Bandeau « Chiffres clés » (ronds) avant le pied de page, toutes les pages.
     Chiffres : Contenus › Chiffres clés. Titre / sur-titre / affichage : Réglages › Textes du site (groupe « figures »). --}}
@if(s('figures.enabled', '1') === '1')
    @php($figures = \App\Models\KeyFigure::forFront())
    @if($figures->isNotEmpty())
        <section class="sec figures" aria-labelledby="figures-title">
            <div class="wrap">
                <div class="sec-head figures-head reveal">
                    @if($eyebrow = s('figures.eyebrow', 'Braises Émeraude en chiffres'))<span class="eyebrow">{{ $eyebrow }}</span>@endif
                    <h2 id="figures-title">{!! em(s('figures.title', 'Des spécialistes du feu *près de chez vous*')) !!}</h2>
                </div>
                <ul class="figures-list reveal" role="list">
                    @foreach($figures as $figure)
                        @php($n = $figure->displayValue())
                        <li class="figure">
                            <div class="figure-ring">
                                @if($figure->prefix)<span class="figure-pre">{{ $figure->prefix }}</span>@endif
                                <span class="figure-num" data-count="{{ $figure->animate ? $n : '' }}" data-suffix="{{ $figure->suffix }}">{{ $n }}{{ $figure->suffix }}</span>
                            </div>
                            <span class="figure-label">{{ $figure->label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
@endif
