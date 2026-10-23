<a class="product-card" href="{{ $product->url() }}">
    @if($product->mainImage())<img src="{{ media($product->mainImage()) }}" alt="{{ $product->name }}" width="400" height="400" loading="lazy">@endif
    <span class="brand">{{ $product->brand?->name }}</span>
    <h3>{{ $product->name }}</h3>
    @if($product->power_kw)<span class="kw">{{ $product->power_kw }} kW</span>@endif
</a>
