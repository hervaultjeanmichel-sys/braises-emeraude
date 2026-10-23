<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AidTopic;
use App\Models\Brand;
use App\Models\City;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Front public. Chaque méthode charge l'entité depuis la base, prépare le SEO
 * (title, description, canonical, robots, fil d'Ariane, schémas) et rend une vue.
 * Aucun texte en dur : tout vient de l'admin.
 */
class FrontController extends Controller
{
    public function home(): View
    {
        $page = Page::published()->where('slug', '/')->firstOrFail();
        $seo = Seo::for($page);
        $seo->schemas[] = Seo::faqSchema($page->faq);

        return view('front.home', [
            'page' => $page,
            'seo' => $seo,
            'stores' => Store::published()->orderBy('sort')->get(),
            'categories' => ProductCategory::published()->whereNull('parent_id')->orderBy('sort')->get(),
            'brands' => Brand::published()->orderBy('sort')->get(),
        ]);
    }

    /** /{slug}/ : page CMS, puis page magasin, puis page ville. */
    public function resolve(string $slug): View
    {
        if ($page = Page::published()->where('slug', $slug)->first()) {
            $seo = Seo::for($page, [['label' => $page->title, 'url' => $page->url()]]);
            $seo->schemas[] = Seo::faqSchema($page->faq);
            $view = view()->exists('front.pages.'.$page->template) ? 'front.pages.'.$page->template : 'front.pages.default';

            return view($view, compact('page', 'seo'));
        }

        if ($store = Store::published()->where('slug', $slug)->first()) {
            $seo = Seo::for($store, [['label' => $store->short_name, 'url' => $store->url()]]);

            return view('front.store', [
                'store' => $store->load('brands'),
                'seo' => $seo,
                'cities' => $store->cities()->published()->orderBy('name')->get(),
            ]);
        }

        $city = City::published()->where('slug', $slug)->with('store.brands')->firstOrFail();
        $seo = Seo::for($city, array_values(array_filter([
            $city->store ? ['label' => $city->store->short_name, 'url' => $city->store->url()] : null,
            ['label' => $city->name, 'url' => $city->url()],
        ])));
        $seo->schemas[] = Seo::faqSchema($city->faq);

        return view('front.city', compact('city', 'seo'));
    }

    public function product(string $slug): View
    {
        $product = Product::published()->where('slug', $slug)->with(['brand', 'category.parent'])->firstOrFail();
        $crumbs = [];
        if ($product->category?->parent) {
            $crumbs[] = ['label' => $product->category->parent->name, 'url' => $product->category->parent->url()];
        }
        if ($product->category) {
            $crumbs[] = ['label' => $product->category->name, 'url' => $product->category->url()];
        }
        $crumbs[] = ['label' => $product->name, 'url' => $product->url()];

        $seo = Seo::for($product, $crumbs);
        $seo->image = $product->mainImage();
        $seo->schemas[] = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->metaDescription(),
            'image' => $product->images ? array_map(fn ($i) => $seo->absolute('/storage/'.$i), $product->images) : null,
            'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
            'category' => $product->category?->name,
        ]);

        $related = Product::published()->where('id', '!=', $product->id)
            ->where(fn ($q) => $q->where('brand_id', $product->brand_id)->orWhere('product_category_id', $product->product_category_id))
            ->limit(4)->get();

        return view('front.product', compact('product', 'seo', 'related'));
    }

    public function category(Request $request, string $path): View
    {
        $category = ProductCategory::published()->where('path', trim($path, '/'))->with('parent', 'children')->firstOrFail();
        $crumbs = [];
        if ($category->parent) {
            $crumbs[] = ['label' => $category->parent->name, 'url' => $category->parent->url()];
        }
        $crumbs[] = ['label' => $category->name, 'url' => $category->url()];
        $seo = Seo::for($category, $crumbs);

        $base = Product::published()->whereIn('product_category_id', $category->selfAndChildrenIds());
        $brandIds = (clone $base)->whereNotNull('brand_id')->distinct()->pluck('brand_id');
        $energies = (clone $base)->whereNotNull('energy')->distinct()->pluck('energy');

        $query = (clone $base)->with('brand');
        $brand = $request->string('marque')->toString();
        $energy = $request->string('energie')->toString();
        if ($brand !== '') {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brand));
        }
        if ($energy !== '') {
            $query->where('energy', $energy);
        }
        // Une liste filtrée n'est pas une page à indexer : la canonique reste la catégorie
        if ($brand !== '' || $energy !== '' || $request->integer('page') > 1) {
            $seo->robots = 'noindex,follow';
        }

        return view('front.category', [
            'category' => $category,
            'seo' => $seo,
            'brands' => Brand::whereIn('id', $brandIds)->orderBy('name')->get(),
            'energies' => $energies,
            'activeBrand' => $brand,
            'activeEnergy' => $energy,
            'products' => $query->orderByDesc('is_featured')->orderBy('name')->paginate(24)->withPath($category->url())->withQueryString(),
        ]);
    }

    public function brand(string $slug): View
    {
        $brand = Brand::published()->where('slug', $slug)->with('stores')->firstOrFail();

        return view('front.brand', [
            'brand' => $brand,
            'seo' => Seo::for($brand, [['label' => 'Marques', 'url' => '/boutique/'], ['label' => $brand->name, 'url' => $brand->url()]]),
            'products' => $brand->products()->published()->with('category')->orderBy('name')->get(),
        ]);
    }

    public function aid(string $slug): View
    {
        $aid = AidTopic::published()->where('slug', $slug)->firstOrFail();
        $seo = Seo::for($aid, [['label' => 'Aides et financement', 'url' => '/aides-financement/'], ['label' => $aid->title, 'url' => $aid->url()]]);
        $seo->schemas[] = Seo::faqSchema($aid->faq);

        return view('front.aid', compact('aid', 'seo'));
    }

    public function post(string $year, string $month, string $day, string $slug): View
    {
        $date = Carbon::createFromFormat('Y-m-d', "$year-$month-$day")->startOfDay();
        $post = Post::published()->where('slug', $slug)
            ->whereBetween('published_at', [$date, $date->copy()->endOfDay()])
            ->firstOrFail();

        $seo = Seo::for($post, [['label' => 'Blog', 'url' => '/'], ['label' => $post->title, 'url' => $post->url()]]);
        $seo->schemas[] = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->h1 ?: $post->title,
            'datePublished' => $post->published_at->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'image' => $post->image ? $seo->absolute('/storage/'.$post->image) : null,
            'publisher' => ['@id' => $seo->absolute('/').'#groupe'],
        ]);
        $seo->schemas[] = Seo::faqSchema($post->faq);

        return view('front.post', compact('post', 'seo'));
    }
}
