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
use Illuminate\Http\Response;

/** Sitemap XML automatique : uniquement les contenus publiés et indexables. */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $base = rtrim((string) config('app.url'), '/');
        $urls = collect();

        $add = function ($items) use ($urls, $base) {
            foreach ($items as $item) {
                if ($item->isIndexable() && ($item->in_sitemap ?? true)) {
                    $urls->push(['loc' => $base.$item->url(), 'lastmod' => $item->updated_at?->toAtomString()]);
                }
            }
        };

        $add(Page::published()->get());
        $add(Store::published()->get());
        $add(City::published()->get());
        $add(ProductCategory::published()->get());
        $add(Product::published()->get());
        $add(Brand::published()->get());
        $add(AidTopic::published()->get());
        $add(Post::published()->where('published_at', '<=', now())->get());

        $urls->push(['loc' => $base.'/devis/', 'lastmod' => null]); // page devis indexable

        $xml = view('front.sitemap', ['urls' => $urls->unique('loc')])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $base = rtrim((string) config('app.url'), '/');
        // Préprod : tout bloquer. Prod : tout ouvrir sauf l'admin.
        $body = app()->environment('production')
            ? "User-agent: *\nDisallow: /admin/\nDisallow: /livewire/\n\nSitemap: {$base}/sitemap.xml\n"
            : "User-agent: *\nDisallow: /\n";

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
