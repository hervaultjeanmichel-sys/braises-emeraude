<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-heart" heading="État du site" :description="$score.' / '.$total.' contrôles au vert'">
        <div style="display:grid;gap:.15rem">
            @foreach($checks as [$label, $status, $detail, $url])
                @php($color = ['ok' => '#22c55e', 'warn' => '#f59e0b', 'error' => '#ef4444', 'info' => '#94a3b8'][$status])
                <div style="display:grid;grid-template-columns:14px minmax(180px,1fr) 2fr;gap:.75rem;align-items:baseline;padding:.4rem 0;border-bottom:1px solid rgba(127,127,127,.12);font-size:.875rem">
                    <span style="width:10px;height:10px;border-radius:50%;background:{{ $color }};display:inline-block" title="{{ $status }}"></span>
                    <span style="font-weight:600">{{ $label }}</span>
                    <span>@if($url)<a href="{{ $url }}" style="text-decoration:underline">{{ $detail }}</a>@else{{ $detail }}@endif</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
