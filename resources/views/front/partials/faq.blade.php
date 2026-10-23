@if(!empty($faq))
<section class="sec faq wrap">
    <h2>{{ s('faq.title', 'Questions fréquentes') }}</h2>
    @foreach($faq as $q)
        <details><summary>{{ $q['question'] ?? '' }}</summary><p>{{ $q['answer'] ?? '' }}</p></details>
    @endforeach
</section>
@endif
