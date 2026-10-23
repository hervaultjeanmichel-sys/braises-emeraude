{{-- Liste « Mon projet » : stockée dans le navigateur (sans compte), rendue en JavaScript. --}}
@extends('layouts.front')
@section('content')
    @include('front.partials.hero', ['eyebrow' => s('project.eyebrow', 'Sans engagement'), 'h1' => s('project.h1', 'Mon *projet*'), 'intro' => s('project.intro', 'Les appareils que vous avez repérés. Ajoutez une remarque, puis demandez un devis pour toute la sélection.')])
    <section class="content">
        <div class="wrap" style="max-width:860px">
            <div class="project-list" data-project-list
                 data-note-label="{{ s('project.note', 'Remarque (couleur, emplacement…)') }}"
                 data-remove-label="{{ s('project.remove', 'Retirer') }}"></div>
            <p class="empty" data-project-empty hidden>{{ s('project.empty', 'Votre sélection est vide pour l\'instant. Parcourez le catalogue et cliquez sur « Ajouter à mon projet ».') }}</p>
            <noscript><p class="empty">{{ s('project.nojs', 'Activez JavaScript pour utiliser la liste de projet, ou demandez directement un devis.') }}</p></noscript>
            <div class="ctas" style="margin-top:2rem">
                <a class="btn btn-main" href="/devis/" data-hover data-project-cta>{{ s('project.cta', 'Demander un devis pour ma sélection') }}</a>
                <a class="btn btn-ghost" href="/chauffage/" data-hover>{{ s('project.more', 'Continuer à parcourir') }}</a>
            </div>
        </div>
    </section>
@endsection
