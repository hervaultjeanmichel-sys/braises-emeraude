<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-sparkles" heading="Effet saisonnier">
        @if($current)
            <p style="font-size:1.4rem;font-weight:700">{{ \App\Models\SeasonalEffect::EFFECTS[$current->effect] ?? $current->effect }}</p>
            <p><strong>{{ $current->name }}</strong> — actif {{ $current->scope === 'home' ? 'sur l’accueil' : 'sur tout le site' }},
                jusqu’au {{ $current->ends_on->locale('fr')->isoFormat('D MMMM') }} (intensité : {{ \App\Models\SeasonalEffect::INTENSITIES[$current->intensity] ?? $current->intensity }})</p>
            <p style="margin-top:.5rem"><a href="/admin/seasonal-effects/{{ $current->id }}/edit" style="color:rgb(var(--primary-500));text-decoration:underline">Modifier ou couper l’effet</a></p>
        @else
            <p>Aucun effet animé en ce moment.</p>
        @endif
        @if($next)
            <p style="margin-top:1rem;opacity:.75">Prochain : <strong>{{ $next->name }}</strong> ({{ \App\Models\SeasonalEffect::EFFECTS[$next->effect] ?? $next->effect }}) à partir du {{ $nextDate->locale('fr')->isoFormat('D MMMM YYYY') }}.</p>
        @endif
        <p style="margin-top:.75rem"><a href="/admin/seasonal-effects" style="color:rgb(var(--primary-500));text-decoration:underline">Calendrier des effets</a></p>
    </x-filament::section>
</x-filament-widgets::widget>
