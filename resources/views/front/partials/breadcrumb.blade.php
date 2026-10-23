@if(!empty($seo->breadcrumb))
<nav class="crumbs wrap" aria-label="{{ s('breadcrumb.aria', 'Fil d\'Ariane') }}">
    <a href="/">{{ s('breadcrumb.home', 'Accueil') }}</a>
    @foreach($seo->breadcrumb as $crumb)
        <span aria-hidden="true">›</span>
        @if($loop->last)<span aria-current="page">{{ $crumb['label'] }}</span>@else<a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>@endif
    @endforeach
</nav>
@endif
