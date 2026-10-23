{{-- Carte produit avec bouton « Ajouter à mon projet ». --}}
<article class="product-card">
    <a class="ph" href="{{ $product->url() }}" tabindex="-1" aria-hidden="true">
        @if($product->mainImage())<img src="{{ media($product->mainImage()) }}" alt="" width="400" height="400" loading="lazy">
        @else @include('front.partials.icon', ['name' => $product->energy === 'granules' ? 'granules' : 'bois']) @endif
    </a>
    <div class="body">
        @if($product->brand)<span class="brand">{{ $product->brand->name }}</span>@endif
        <h3><a href="{{ $product->url() }}">{{ $product->name }}</a></h3>
        <span class="meta">{{ collect([
            $product->power_kw ? rtrim(rtrim(number_format($product->power_kw, 1, ',', ''), '0'), ',').' kW' : null,
            $product->surface_max_m2 ? s('product.up_to', 'jusqu\'à').' '.$product->surface_max_m2.' m²' : null,
            $product->energy ? s('energy.'.$product->energy, ucfirst($product->energy)) : null,
        ])->filter()->implode(' · ') }}</span>
    </div>
    <button class="add" type="button" aria-label="{{ s('cta.add_project', 'Ajouter à mon projet') }} : {{ $product->name }}"
        data-project-add="{{ $product->id }}" data-name="{{ $product->name }}" data-brand="{{ $product->brand?->name }}"
        data-url="{{ $product->url() }}" data-img="{{ media($product->mainImage()) }}">@include('front.partials.icon', ['name' => 'plus'])</button>
</article>
