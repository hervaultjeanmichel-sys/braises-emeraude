<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-chart-bar" heading="Audience — 7 derniers jours">
        @php($totalSrc = max(1, $sources->sum('v')))
        <div style="display:grid;gap:1.25rem">
            <div>
                <p style="font-weight:600;margin-bottom:.4rem">Pages les plus vues</p>
                @forelse($pages as $p)
                    <div style="display:flex;justify-content:space-between;gap:1rem;font-size:.875rem;padding:.2rem 0;border-bottom:1px solid rgba(127,127,127,.15)">
                        <a href="{{ $p->path }}" target="_blank" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p->path }}</a><strong>{{ $p->v }}</strong>
                    </div>
                @empty
                    <p style="opacity:.7">Pas encore de visite enregistrée.</p>
                @endforelse
            </div>
            @if($sources->isNotEmpty())
                <div>
                    <p style="font-weight:600;margin-bottom:.4rem">Provenance des visiteurs</p>
                    @foreach($sources as $src)
                        @php($pct = round($src->v / $totalSrc * 100))
                        <div style="font-size:.875rem;margin-bottom:.35rem">
                            <div style="display:flex;justify-content:space-between"><span>{{ $src->source ?: 'direct' }}</span><span>{{ $pct }} %</span></div>
                            <div style="height:6px;border-radius:3px;background:rgba(127,127,127,.15)"><div style="height:6px;border-radius:3px;width:{{ $pct }}%;background:rgb(var(--primary-500))"></div></div>
                        </div>
                    @endforeach
                    <p style="font-size:.8rem;opacity:.7;margin-top:.4rem">{{ $devices->map(fn ($d) => ucfirst($d->device).' : '.round($d->v / $totalSrc * 100).' %')->implode(' · ') }}</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
