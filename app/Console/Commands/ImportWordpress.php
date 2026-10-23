<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Reprise des contenus du WordPress actuel via son API REST publique :
 * textes, images (converties en WebP), titres et descriptions Yoast.
 * Ne remplit que les champs vides, sauf avec --force.
 *
 *   php artisan braises:import-wordpress --dry-run
 *   php artisan braises:import-wordpress
 *   php artisan braises:import-wordpress --only=products --no-images
 */
class ImportWordpress extends Command
{
    protected $signature = 'braises:import-wordpress
        {--only= : pages, posts, products ou categories}
        {--force : écrase les champs déjà remplis}
        {--no-images : ne télécharge pas les images}
        {--dry-run : affiche sans enregistrer}';

    protected $description = 'Importe les contenus de braisesemeraude.com (WordPress / WooCommerce)';

    private string $base;

    private int $written = 0;

    public function handle(): int
    {
        $this->base = rtrim((string) config('braises.wordpress_url'), '/');
        $only = $this->option('only');

        foreach (['categories', 'products', 'pages', 'posts'] as $step) {
            if (! $only || $only === $step) {
                $this->info("— {$step}");
                $this->{$step}();
            }
        }

        $this->info("Terminé : {$this->written} fiche(s) mise(s) à jour".($this->option('dry-run') ? ' (simulation)' : ''));

        return self::SUCCESS;
    }

    /** Récupère toutes les pages d'un endpoint REST (pagination WordPress). */
    private function fetchAll(string $endpoint, array $query = []): array
    {
        $items = [];
        $page = 1;
        do {
            $res = Http::timeout(30)->acceptJson()->get($this->base.$endpoint, [...$query, 'per_page' => 100, 'page' => $page]);
            if (! $res->ok()) {
                if ($page === 1) {
                    $this->warn("  {$endpoint} : HTTP {$res->status()}");
                }
                break;
            }
            $batch = $res->json() ?? [];
            $items = [...$items, ...$batch];
            $total = (int) ($res->header('X-WP-TotalPages') ?: 1);
            $page++;
        } while ($batch !== [] && $page <= $total);

        return $items;
    }

    private function fill(object $model, array $values): void
    {
        foreach ($values as $k => $v) {
            if ($v === null || $v === '' || $v === []) {
                continue;
            }
            if ($this->option('force') || blank($model->{$k})) {
                $model->{$k} = $v;
            }
        }
        if ($model->isDirty()) {
            $this->line('  ✓ '.($model->slug ?? $model->id).' : '.implode(', ', array_keys($model->getDirty())));
            $this->written++;
            if (! $this->option('dry-run')) {
                $model->save();
            }
        }
    }

    private function yoast(array $item): array
    {
        $y = $item['yoast_head_json'] ?? [];

        return [
            'seo_title' => isset($y['title']) ? html_entity_decode($y['title']) : null,
            'seo_description' => isset($y['description']) ? html_entity_decode($y['description']) : null,
        ];
    }

    private function clean(?string $html): ?string
    {
        if (! $html) {
            return null;
        }
        // Retire les shortcodes et commentaires Gutenberg / Elementor
        $html = preg_replace(['/\[\/?[a-z_\-]+[^\]]*\]/i', '/<!--.*?-->/s'], '', $html);

        return trim((string) $html) ?: null;
    }

    /** Télécharge une image et la convertit en WebP (qualité 82). */
    private function image(?string $url, string $dir): ?string
    {
        if (! $url || $this->option('no-images') || $this->option('dry-run')) {
            return null;
        }
        $name = $dir.'/'.Str::slug(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME)).'.webp';
        if (Storage::disk('public')->exists($name)) {
            return $name;
        }
        $res = Http::timeout(60)->get($url);
        if (! $res->ok() || ! ($img = @imagecreatefromstring($res->body()))) {
            $this->warn("  image ignorée : {$url}");

            return null;
        }
        imagepalettetotruecolor($img);
        imagesavealpha($img, true);
        ob_start();
        imagewebp($img, null, 82);
        Storage::disk('public')->put($name, (string) ob_get_clean());
        imagedestroy($img);

        return $name;
    }

    private function categories(): void
    {
        foreach ($this->fetchAll('/wp-json/wc/store/v1/products/categories') as $c) {
            $cat = ProductCategory::where('slug', $c['slug'])->first();
            if ($cat) {
                $this->fill($cat, [
                    'name' => html_entity_decode($c['name']),
                    'content' => $this->clean($c['description'] ?? null),
                    'image' => $this->image($c['image']['src'] ?? null, 'categories'),
                ]);
            }
        }
    }

    private function products(): void
    {
        $yoast = collect($this->fetchAll('/wp-json/wp/v2/product', ['_fields' => 'slug,yoast_head_json']))->keyBy('slug');
        $brands = Brand::all();
        $cleanSlugs = ['jazz-mural-turbo-fonte-copie' => 'jazz-mural-turbo-fonte', 'club-comfort-air-12-m2-copie' => 'club-comfort-air-12-m2', 'cuisiniere-rizolli-serie-m' => 'cuisiniere-rizzoli-serie-m'];

        foreach ($this->fetchAll('/wp-json/wc/store/v1/products') as $p) {
            $slug = $cleanSlugs[$p['slug']] ?? $p['slug'];
            $product = Product::firstOrNew(['slug' => $slug]);
            if (! $product->exists) {
                $this->warn("  produit absent de l'inventaire, créé : {$slug}");
            }

            // Catégorie : la plus profonde connue en base
            $catId = null;
            foreach ($p['categories'] ?? [] as $c) {
                $found = ProductCategory::where('slug', $c['slug'])->orderByDesc('parent_id')->first();
                if ($found && (! $catId || $found->parent_id)) {
                    $catId = $found->id;
                }
            }
            // Marque : nom de marque trouvé dans le titre
            $brandId = $brands->first(fn ($b) => Str::contains(Str::lower($p['name']), Str::lower($b->name)))?->id;

            $images = [];
            foreach (array_slice($p['images'] ?? [], 0, 8) as $img) {
                if ($path = $this->image($img['src'] ?? null, 'produits')) {
                    $images[] = $path;
                }
            }

            $this->fill($product, [
                'name' => html_entity_decode($p['name']),
                'h1' => html_entity_decode($p['name']),
                'excerpt' => trim(strip_tags($this->clean($p['short_description'] ?? '') ?? '')) ?: null,
                'content' => $this->clean($p['description'] ?? null),
                'product_category_id' => $catId,
                'brand_id' => $brandId,
                'images' => $images,
                ...$this->yoast($yoast[$p['slug']] ?? []),
            ]);
        }
    }

    private function pages(): void
    {
        foreach ($this->fetchAll('/wp-json/wp/v2/pages', ['_fields' => 'slug,title,content,excerpt,yoast_head_json,link']) as $wp) {
            $path = trim((string) parse_url($wp['link'] ?? '', PHP_URL_PATH), '/');
            $target = $path === '' ? Page::where('slug', '/')->first()
                : (Page::where('slug', $path)->first() ?? Store::where('slug', $path)->first() ?? City::where('slug', $path)->first());
            if (! $target) {
                continue; // doublons « -2 » : gérés par redirection
            }
            $this->fill($target, [
                'content' => $this->clean($wp['content']['rendered'] ?? null),
                ...$this->yoast($wp),
            ]);
        }
    }

    private function posts(): void
    {
        foreach ($this->fetchAll('/wp-json/wp/v2/posts', ['_embed' => 1]) as $wp) {
            $post = Post::where('slug', $wp['slug'])->first();
            if (! $post) {
                continue;
            }
            $this->fill($post, [
                'title' => html_entity_decode($wp['title']['rendered'] ?? ''),
                'excerpt' => trim(strip_tags($wp['excerpt']['rendered'] ?? '')) ?: null,
                'content' => $this->clean($wp['content']['rendered'] ?? null),
                'image' => $this->image($wp['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null, 'blog'),
                ...$this->yoast($wp),
            ]);
        }
    }
}
