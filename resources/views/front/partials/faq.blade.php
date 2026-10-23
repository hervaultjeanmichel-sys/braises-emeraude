@if(!empty($faq))
<div class="faq reveal">
    <h2 style="font-size:1.9rem;margin-bottom:.6rem">{{ $title ?? s('faq.title', 'Vos questions') }}</h2>
    @foreach($faq as $q)
        <details @if($loop->first) open @endif><summary>{{ $q['question'] ?? '' }}</summary><p>{{ $q['answer'] ?? '' }}</p></details>
    @endforeach
</div>
@endif
