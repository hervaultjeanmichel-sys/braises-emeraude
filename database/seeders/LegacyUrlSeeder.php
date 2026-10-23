<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\City;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Redirect;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Recrée les ~148 URLs de braisesemeraude.com (inventaire Yoast du 22/10/2026).
 * Idempotent : peut être relancé, ne remplace jamais un texte déjà saisi.
 * Les textes, images et SEO sont ensuite repris par `php artisan braises:import-wordpress`.
 */
class LegacyUrlSeeder extends Seeder
{
    public function run(): void
    {
        $this->stores();
        $this->categories();
        $this->brands();
        $this->products();
        $this->pages();
        $this->cities();
        $this->posts();
        $this->redirects();
    }

    /** Crée la fiche si absente, sans écraser ce qui a été modifié dans l'admin. */
    private function upsert(string $model, array $keys, array $values): mixed
    {
        $record = $model::firstOrNew($keys);
        foreach ($values as $k => $v) {
            if (blank($record->{$k})) {
                $record->{$k} = $v;
            }
        }
        $record->save();

        return $record;
    }

    private function stores(): void
    {
        $mf = fn (string $o1, string $c1, string $o2, string $c2) => collect(range(1, 5))
            ->flatMap(fn ($d) => [['day' => $d, 'opens' => $o1, 'closes' => $c1], ['day' => $d, 'opens' => $o2, 'closes' => $c2]])->all();

        // ⚠️ Coordonnées à confirmer avec le client (question n°3 du cahier des charges)
        $this->upsert(Store::class, ['slug' => 'poele-a-bois-granules-dinard'], [
            'name' => 'Braises Émeraude Dinard · Pleurtuit', 'short_name' => 'Dinard', 'area_label' => 'Dinard · Pleurtuit',
            'postal_code' => '35730', 'city' => 'Pleurtuit', 'phone' => '02 99 73 97 81',
            'served_communes' => ['Dinard', 'Pleurtuit', 'Saint-Briac-sur-Mer', 'Saint-Lunaire'],
            'served_postcodes' => ['35730', '35800', '35780', '35560', '22490', '22770'],
            'h1' => 'Poêle à bois et à granulés à Dinard', 'sort' => 1,
        ]);
        $this->upsert(Store::class, ['slug' => 'poele-a-bois-granules-saint-malo'], [
            'name' => 'Braises Émeraude Saint-Malo · Baguer-Pican', 'short_name' => 'Saint-Malo', 'area_label' => 'Saint-Malo · Baguer-Pican',
            'address' => 'La Hertaudière', 'postal_code' => '35120', 'city' => 'Baguer-Morvan', 'phone' => '02 23 15 28 66',
            'opening_hours' => [...$mf('09:00', '12:00', '14:00', '18:00'), ['day' => 6, 'opens' => '09:00', 'closes' => '12:30']],
            'served_communes' => ['Saint-Malo', 'Dol-de-Bretagne', 'Cancale', 'Combourg'],
            'served_postcodes' => ['35400', '35120', '35260', '35270', '35430', '35350'],
            'h1' => 'Poêle à bois et à granulés à Saint-Malo', 'sort' => 2,
        ]);
        $this->upsert(Store::class, ['slug' => 'poele-a-bois-granules-rennes'], [
            'name' => 'Braises Émeraude Rennes', 'short_name' => 'Rennes', 'area_label' => 'Rennes Métropole',
            'city' => 'Rennes', 'is_new' => true, 'badge_label' => 'Nouveau',
            'served_communes' => ['Rennes', 'Cesson-Sévigné', 'Saint-Grégoire', 'Chantepie'],
            'served_postcodes' => ['35000', '35200', '35700', '35510', '35760', '35135', '35740', '35650'],
            'h1' => 'Poêle à bois et à granulés à Rennes', 'sort' => 3,
        ]);
    }

    private function categories(): void
    {
        $tree = [
            ['chauffage-bois', 'Chauffage bois', [['poele-chauffage-bois', 'Poêles à bois'], ['insert-chauffage-bois', 'Inserts à bois']]],
            ['chauffage-granule', 'Chauffage granulés', [['poele', 'Poêles à granulés'], ['insert', 'Inserts à granulés']]],
            ['chauffage-mixte', 'Chauffage mixte', []],
            ['cuisson', 'Cuisson', []],
        ];
        foreach ($tree as $i => [$slug, $name, $children]) {
            $parent = $this->upsert(ProductCategory::class, ['path' => $slug], ['slug' => $slug, 'name' => $name, 'sort' => $i]);
            foreach ($children as $j => [$cslug, $cname]) {
                $this->upsert(ProductCategory::class, ['path' => "$slug/$cslug"], ['slug' => $cslug, 'name' => $cname, 'parent_id' => $parent->id, 'sort' => $j]);
            }
        }
    }

    private const BRANDS = [
        'austroflamm' => 'Austroflamm', 'romotop' => 'Romotop', 'dovre' => 'Dovre', 'brunner' => 'Brunner',
        'turbo-fonte' => 'Turbo Fonte', 'lorflam' => 'Lorflam', 'mcz' => 'MCZ', 'stoveitaly' => 'Stoveitaly',
        'sergio-leoni' => 'Sergio Leoni', 'jide' => 'Jide', 'rizzoli' => 'Rizzoli',
    ];

    private function brands(): void
    {
        $i = 0;
        foreach (self::BRANDS as $slug => $name) {
            $this->upsert(Brand::class, ['slug' => $slug], ['name' => $name, 'sort' => $i++]);
        }
    }

    /** 106 slugs produits de l'inventaire. Les 3 slugs mal formés sont nettoyés (301 plus bas). */
    private const PRODUCTS = [
        'clou-xtra-2-0-austroflamm', 'heidi-back-austroflamm', 'evora-t-romotop', 'evora-romotop', 'clou-xtra-back-austroflamm',
        'clou-compact-cook-austroflamm', 'clou-compact-2-0-austroflamm', 'callisto-2-turbo-fonte', 'burgos-romotop', 'alpera-g-romotop',
        'alpera-e-romotop', 'alea-romotop', 'alea-accumulation-romotop', '760-wd-dovre', '640-wd-dovre', 'foyer-double-face-brunner',
        'foyer-panorama-brunner', 'foyer-dangle-brunner', 'foyer-c2gl-romotop', 'foyer-2gl-romotop', 'foyer-2g-romotop',
        'jazz-mural-turbo-fonte', 'xp-box-lorflam', 'tivoli-cv-turbo-fonte', 'riano-romotop', 'stromboli-romotop', 'riano-n-romotop',
        'ovalis-t-romotop', 'ovalis-romotop', 'orion-2-turbo-fonte', 'navia-romotop', 'mably-mural-turbo-fonte', 'laredo-t-romotop',
        'laredo-romotop', 'karl-100-df-turbo-fonte', 'iron-dog-n6-brunner', 'piano-insert', 'gaio-insert', 'gaio-glass-insert',
        'boxtherm-70-air-9-basic-m1', 'vivo-90-pellet-comfort-air-mcz', 'may-air7-s1', 'maria-sofia-pv-8-m1', 'oto-plus-stoveitaly',
        'insert-vitre-stoveitaly', 'musa-comfort-air-12-m2', 'raam-comfort-air-8-up-s1', 'ray-comfort-air-8-up-m1', 'sissy-p-i-sergio-leoni',
        'stream-comfort-air-12-m1', 'tilda-comfort-air-10-m1-mcz', 'ofen-sp1-lorflam', 'ofen-sp2-lorflam', 'ofen-sp3-lorflam',
        'piano-glass-stoveitaly', 'cute-air-8-mcz', 'poele-deco-air-8-mcz', 'poele-duetto-9-stoveitaly', 'poele-coke-gaio-glass-stoveitaly',
        'star-comfort-air-10-m2', 'ego-comfort-air-10-mcz', 'flow-comfort-air-m10', 'halo-air-8-mcz', 'maria-costanza-sergio-leoni',
        'marlene-tonda-pv-8-m1-sergio-leoni', 'marlene-e', 'club-comfort-air-12-m2', 'doc-comfort-air-9-m1', 'ofen-sp4-lorflam',
        'tecla-air-6-mcz', 'suite-comfort-air-12-mcz', 'curve-comfort-air-8-mcz', 'compact-glass-stoveitaly', 'alaska-glass-stoveitaly',
        'clou-compact-pellets-austroflamm', 'clou-pellet-austroflamm', 'alea-air-7-s1-xup-mcz', 'vienne-austroflamm', 'evora-accum-romotop',
        'laredo-accumulation', 'ovalis-g-accum-romotop', 'jazz-socle-turbo-fonte', 'jazz-xl-turbo-fonte', 'iron-dog-n4-brunner',
        'foyer-a-bois-jide-decor-plus', 'foyer-a-bois-jide-lodge', 'foyer-a-bois-jide-loft', 'foyer-a-bois-jide-nordic-plus',
        'foyer-a-bois-jide-pure', 'insert-a-bois-jide-corner', 'poele-a-bois-jide-modul-art', '2620scb-dovre', '2120s-dovre', '2020s-dovre',
        'clou-duo-austroflamm', 'mo-duo-cook-austroflamm', 'mo-duo-austroflamm', 'scotty-duo-austroflamm', 'alyssa-mcz', 'xp78-cadre-lorflam',
        'insert-dovre-2520-sb', 'cuisiniere-rizzoli-serie-m', 'cuisiniere-rizzoli-serie-n', 'cuisiniere-a-bois-rizzoli-serie-r',
        'cuisiniere-a-bois-rizzoli-serie-s', 'cuisiniere-a-bois-rizzoli-serie-l', 'cuisiniere-a-bois-rizzoli-serie-zvi',
    ];

    /** Déduction provisoire de la marque depuis le slug ; corrigée par l'import WordPress puis dans l'admin. */
    private function guessBrand(string $slug): ?string
    {
        foreach (array_keys(self::BRANDS) as $b) {
            if (str_contains($slug, $b)) {
                return $b;
            }
        }

        return match (true) {
            str_contains($slug, 'comfort-air'), str_contains($slug, 'boxtherm'), str_starts_with($slug, 'may-air') => 'mcz',
            str_starts_with($slug, 'maria-'), str_starts_with($slug, 'marlene') => 'sergio-leoni',
            str_starts_with($slug, 'piano-'), str_starts_with($slug, 'gaio-') => 'stoveitaly',
            str_starts_with($slug, 'laredo') => 'romotop',
            default => null,
        };
    }

    private function guessCategory(string $slug): ?string
    {
        return match (true) {
            str_contains($slug, 'cuisiniere') || str_contains($slug, '-cook') => 'cuisson',
            str_contains($slug, 'duo') || str_contains($slug, 'mixte') => 'chauffage-mixte',
            default => null,
        };
    }

    private function products(): void
    {
        $brands = Brand::pluck('id', 'slug');
        $cats = ProductCategory::pluck('id', 'path');
        foreach (self::PRODUCTS as $slug) {
            $brand = $this->guessBrand($slug);
            $cat = $this->guessCategory($slug);
            $name = Str::of($slug)->replace('-', ' ')->title()->replace(['Mcz', 'Pv ', ' Df'], ['MCZ', 'PV ', ' DF'])->toString();
            $this->upsert(Product::class, ['slug' => $slug], [
                'name' => $name,
                'brand_id' => $brand ? $brands[$brand] ?? null : null,
                'product_category_id' => $cat ? $cats[$cat] ?? null : null,
                'appliance_type' => str_contains($slug, 'cuisiniere') ? 'cuisiniere' : (str_contains($slug, 'insert') ? 'insert' : (str_contains($slug, 'foyer') ? 'foyer' : null)),
            ]);
        }
    }

    private function pages(): void
    {
        $pages = [
            ['/', 'Accueil', 'home'], ['notre-entreprise', 'Notre entreprise', 'default'], ['showroom', 'Showroom', 'default'],
            ['chauffage', 'Chauffage', 'univers'], ['chauffage-granules', 'Chauffage granulés', 'univers'], ['chauffage-bois', 'Chauffage bois', 'univers'],
            ['chauffage-mixte', 'Chauffage mixte', 'univers'], ['chauffage-gaz', 'Chauffage gaz', 'univers'], ['cuisson', 'Cuisson', 'univers'],
            ['boutique', 'Boutique', 'default'], ['nos-realisations', 'Nos réalisations', 'realisations'], ['aides-financement', 'Aides et financement', 'default'],
            ['contact', 'Contact', 'contact'], ['mentions-legales', 'Mentions légales', 'legal'],
            ['politique-de-confidentialite', 'Politique de confidentialité', 'legal'],
            ['poele-ecologique-rennes', 'Poêle écologique à Rennes', 'default'], ['insert-cheminee-rennes', 'Insert de cheminée à Rennes', 'default'],
        ];
        foreach ($pages as [$slug, $title, $tpl]) {
            $this->upsert(Page::class, ['slug' => $slug], [
                'title' => $title, 'template' => $tpl, 'h1' => $title,
                'robots' => $tpl === 'legal' ? 'noindex,follow' : 'index,follow',
                'in_sitemap' => $tpl !== 'legal',
            ]);
        }

        // Sections éditables de l'accueil Groupe (textes repris de la maquette Contrastes, modifiables dans l'admin)
        $home = Page::where('slug', '/')->first();
        if ($home && empty($home->sections)) {
            $home->sections = [
                ['type' => 'hero', 'data' => [
                    'eyebrow' => 'Le Groupe · 3 magasins en Ille-et-Vilaine', 'title' => 'Poêles à bois et à granulés', 'title_accent' => 'de Dinard à Rennes',
                    'lead' => 'Vente, installation et entretien de poêles, inserts et cuisinières. Devis gratuit après visite technique.',
                    'cta_label' => 'Demander un devis gratuit', 'cta_url' => '/contact/', 'sticker' => 'Le feu, c\'est la vie !',
                    'trust' => [['value' => '3', 'label' => 'showrooms'], ['value' => '11', 'label' => 'marques'], ['value' => '106', 'label' => 'appareils']],
                    'slides' => [],
                ]],
                ['type' => 'stores', 'data' => ['eyebrow' => 'Le Groupe', 'title' => 'Trois magasins, une seule marque', 'text' => 'Choisissez le showroom le plus proche : même équipe, mêmes conseils, même service après-vente.']],
                ['type' => 'categories', 'data' => ['eyebrow' => 'Nos univers', 'title' => 'Bois, granulés, mixte ou cuisson', 'text' => null]],
                ['type' => 'brands', 'data' => ['eyebrow' => 'Nos marques', 'title' => 'Les fabricants que nous installons']],
            ];
            $home->save();
        }
    }

    private function cities(): void
    {
        $stMalo = Store::where('slug', 'poele-a-bois-granules-saint-malo')->value('id');
        // Page existante, conservée sous le même slug
        $this->upsert(City::class, ['slug' => 'poele-a-bois-granules-cancale'], [
            'name' => 'Cancale', 'postal_code' => '35260', 'store_id' => $stMalo, 'h1' => 'Poêle à bois et à granulés à Cancale',
        ]);
    }

    private function posts(): void
    {
        $posts = [
            ['2026-07-08', 'poele-a-bois-granules-a-cancale-35', 'Poêle à bois ou à granulés à Cancale'],
            ['2026-07-08', 'aides-financieres-poele-a-bois-granules-cancale', 'Aides financières pour un poêle à Cancale'],
            ['2026-07-08', 'blog-poele-bois-ou-granules-rennes', 'Poêle à bois ou à granulés à Rennes'],
            ['2026-07-09', 'blog-chauffage-bois-ecologique-saint-malo', 'Chauffage au bois écologique à Saint-Malo'],
            ['2026-08-31', 'poele-bois-ou-granules-rennes-differences-quotidien', 'Poêle à bois ou à granulés à Rennes : les différences au quotidien'],
        ];
        foreach ($posts as [$date, $slug, $title]) {
            if (! Post::where('slug', $slug)->exists()) {
                Post::create(['slug' => $slug, 'title' => $title, 'h1' => $title, 'published_at' => $date.' 09:00:00']);
            }
        }
    }

    private function redirects(): void
    {
        $map = [
            // Doublons WordPress et URL parasite
            '/politique-de-confidentialite-2/' => '/politique-de-confidentialite/',
            '/braises-emeraude-2/' => '/',
            '/?page_id=82' => '/',
            // Pages sans valeur
            '/category/non-classe/' => '/',
            '/flamme-verte/7/' => '/chauffage/',
            '/etiquette-produit/bois/' => '/categorie-produit/chauffage-bois/',
            '/etiquette-produit/poele-a-bois/' => '/categorie-produit/chauffage-bois/poele-chauffage-bois/',
            '/etiquette-produit/poele-hybride/' => '/categorie-produit/chauffage-mixte/',
            '/etiquette-produit/poele-mixte/' => '/categorie-produit/chauffage-mixte/',
            '/etiquette-produit/saint-malo/' => '/poele-a-bois-granules-saint-malo/',
            // Slugs produits nettoyés
            '/produit/jazz-mural-turbo-fonte-copie/' => '/produit/jazz-mural-turbo-fonte/',
            '/produit/club-comfort-air-12-m2-copie/' => '/produit/club-comfort-air-12-m2/',
            '/produit/cuisiniere-rizolli-serie-m/' => '/produit/cuisiniere-rizzoli-serie-m/',
            // URLs WordPress techniques
            '/shop/' => '/boutique/',
            '/feed/' => '/',
        ];
        foreach ($map as $from => $to) {
            Redirect::updateOrCreate(['from_path' => $from], ['to_path' => $to, 'status_code' => 301, 'note' => 'Reprise WordPress 22/10/2026']);
        }
    }
}
