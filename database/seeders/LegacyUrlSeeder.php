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
            'h1' => 'Poêle à bois et à granulés à Dinard', 'sort' => 1, 'region_label' => 'Côte d\'Émeraude',
            'opening_hours' => [...$mf('09:00', '12:00', '14:00', '18:00'), ['day' => 6, 'opens' => '09:00', 'closes' => '12:30']],
            'card_text' => 'Poêles à bois et à granulés pour Dinard, Saint-Lunaire, Saint-Briac et toute la rive gauche de la Rance.',
        ]);
        $this->upsert(Store::class, ['slug' => 'poele-a-bois-granules-saint-malo'], [
            'name' => 'Braises Émeraude Saint-Malo · Baguer-Pican', 'short_name' => 'Saint-Malo', 'area_label' => 'Saint-Malo · Baguer-Pican',
            'address' => 'La Hertaudière', 'postal_code' => '35120', 'city' => 'Baguer-Morvan', 'phone' => '02 23 15 28 66',
            'opening_hours' => [...$mf('09:00', '12:00', '14:00', '18:00'), ['day' => 6, 'opens' => '09:00', 'closes' => '12:30']],
            'served_communes' => ['Saint-Malo', 'Dol-de-Bretagne', 'Cancale', 'Combourg'],
            'served_postcodes' => ['35400', '35120', '35260', '35270', '35430', '35350'],
            'h1' => 'Poêle à bois et à granulés à Saint-Malo', 'sort' => 2, 'region_label' => 'Pays malouin',
            'card_text' => 'Notre plus grand showroom, avec vente de granulés sur place. Pour Saint-Malo, Cancale, Dol-de-Bretagne et Combourg.',
        ]);
        $this->upsert(Store::class, ['slug' => 'poele-a-bois-granules-rennes'], [
            'name' => 'Braises Émeraude Rennes', 'short_name' => 'Rennes', 'area_label' => 'Rennes Métropole',
            'city' => 'Rennes', 'is_new' => true, 'badge_label' => 'Nouveau',
            'served_communes' => ['Rennes', 'Cesson-Sévigné', 'Saint-Grégoire', 'Chantepie'],
            'served_postcodes' => ['35000', '35200', '35700', '35510', '35760', '35135', '35740', '35650'],
            'h1' => 'Poêle à bois et à granulés à Rennes', 'sort' => 3, 'region_label' => 'Rennes Métropole',
            'announcement' => 'Inauguration en novembre 2026',
            'card_text' => 'Le petit dernier du groupe, au cœur de la métropole. Poêles, inserts et conseils pour Rennes et ses communes voisines.',
        ]);
    }

    private function categories(): void
    {
        $tree = [
            ['chauffage-bois', 'Poêles à bois', [['poele-chauffage-bois', 'Poêles à bois'], ['insert-chauffage-bois', 'Inserts à bois']], 'bois',
                'La vraie flamme et le crépitement des bûches. Poêles, inserts et foyers à fort rendement.'],
            ['chauffage-granule', 'Poêles à granulés', [['poele', 'Poêles à granulés'], ['insert', 'Inserts à granulés']], 'granules',
                'Programmables, autonomes, pilotables depuis votre téléphone. Le chauffage principal sans contrainte.'],
            ['chauffage-mixte', 'Poêles mixtes', [], 'mixte', 'Bois et granulés dans un même appareil. Le charme des bûches, la souplesse des pellets.'],
            ['cuisson', 'Cuisson', [], 'cuisson', 'Cuisinières à bois l\'hiver, barbecues et braseros l\'été. Le feu toute l\'année.'],
        ];
        foreach ($tree as $i => [$slug, $name, $children, $icon, $card]) {
            $parent = $this->upsert(ProductCategory::class, ['path' => $slug], ['slug' => $slug, 'name' => $name, 'sort' => $i, 'icon' => $icon, 'card_text' => $card, 'h1' => $name]);
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

        // Accueil Groupe : textes de la maquette Contrastes validée, tous modifiables dans l'admin
        $home = Page::where('slug', '/')->first();
        if ($home && empty($home->sections)) {
            $home->h1 = 'Poêles à bois et à granulés à Saint-Malo, Dinard et Rennes';
            $home->seo_title = 'Poêles à bois et à granulés · Saint-Malo, Dinard, Rennes';
            $home->seo_description = 'Braises Émeraude, le groupe spécialiste des poêles à bois et à granulés en Ille-et-Vilaine : 3 showrooms à Dinard, Saint-Malo et Rennes. Vente, installation, entretien. Devis gratuit.';
            $home->sections = [
                ['type' => 'saga', 'data' => [
                    'kicker' => 'Braises Émeraude présente',
                    'episodes' => [
                        ['label' => 'Épisode I', 'title' => 'Le Maître du Feu', 'scene' => 'fire', 'rating' => 'Énergie 100 % renouvelable',
                            'tagline' => 'Il ne craint ni le crachin ni le vent d\'ouest. Il dompte la flamme avec le bois de nos forêts bretonnes, et la maison tient bon tout l\'hiver.'],
                        ['label' => 'Épisode II', 'title' => 'Le Gardien du Foyer', 'scene' => 'stove', 'rating' => 'Granulés issus de résidus de bois',
                            'tagline' => 'Dehors, il pleut sur Rennes. Dedans, 21 degrés pile devant une vraie flamme, grâce à des granulés nés de la sciure recyclée des scieries.'],
                        ['label' => 'Épisode III', 'title' => 'Le Retour du Feu', 'scene' => 'forest', 'rating' => 'Le bois, l\'énergie qui repousse',
                            'tagline' => 'La forêt donne le bois, le bois donne la chaleur, et la forêt repousse. Cet hiver, la flamme revient. Dès novembre à Rennes.'],
                    ],
                    'h1' => 'Poêles à bois et à granulés à Saint-Malo, Dinard et Rennes',
                    'billing' => 'Une production *Braises Émeraude* · avec les équipes de *Dinard*, *Saint-Malo* et *Rennes* · poêles signés *Austroflamm*, *MCZ*, *Romotop*, *Dovre* · effets spéciaux : *le bois*',
                    'cta_label' => 'Réserver ma séance (devis gratuit)', 'cta_url' => '/devis/',
                ]],
                ['type' => 'stores', 'data' => ['eyebrow' => 'Le Groupe', 'title' => 'Trois magasins, *une seule* marque',
                    'text' => 'De la Côte d\'Émeraude à la métropole rennaise, chaque showroom expose ses poêles en fonctionnement et dispose de sa propre équipe de pose et d\'entretien.',
                    'link_label' => 'Voir le magasin de :magasin']],
                ['type' => 'categories', 'data' => ['eyebrow' => 'Nos univers', 'title' => 'Choisissez votre *flamme*', 'more_label' => 'Découvrir →', 'light' => true]],
                ['type' => 'quote', 'data' => ['big' => 'Avec Braises Émeraude, vous serez *au poil* devant votre poêle.', 'small' => '(Ou à poil, on ne juge pas.)',
                    'hand' => 'Le feu, c\'est la vie !', 'image' => '/img/campagne-chien.webp', 'image_alt' => 'Golden retriever heureux devant un poêle à bois allumé']],
                ['type' => 'marquee', 'data' => ['aria' => 'Nos marques']],
                ['type' => 'steps', 'data' => ['eyebrow' => 'Notre méthode', 'title' => 'De la visite au *premier feu*', 'items' => [
                    ['title' => 'Visite technique', 'text' => 'Un technicien vient chez vous : conduit, surface, isolation, emplacement idéal.'],
                    ['title' => 'Devis gratuit', 'text' => 'Un devis clair, envoyé rapidement, avec les aides auxquelles vous avez droit.'],
                    ['title' => 'Installation', 'text' => 'Pose par nos propres équipes, tubage et raccordement dans les règles de l\'art.'],
                    ['title' => 'Entretien & ramonage', 'text' => 'Nous suivons votre appareil chaque saison. Granulés disponibles en magasin.'],
                ]]],
                ['type' => 'aids', 'data' => ['eyebrow' => 'Aides & financement', 'title' => 'Votre poêle coûte *moins cher* que vous ne le pensez',
                    'text' => 'Selon la réglementation en vigueur, un poêle performant peut ouvrir droit à des aides de l\'État et des collectivités. Nous faisons le calcul avec vous lors du devis.',
                    'cta_label' => 'Tout savoir sur les aides', 'cta_url' => '/aides-financement/',
                    'bullets' => ['Aides nationales à la rénovation énergétique', 'Primes énergie des fournisseurs', 'TVA réduite sur la pose', 'Solutions de financement en plusieurs fois']]],
                ['type' => 'seo_faq', 'data' => ['eyebrow' => 'Spécialiste du chauffage au bois', 'title' => 'Votre installateur de poêles en Ille-et-Vilaine', 'faq_title' => 'Vos questions',
                    'body' => '<p>Braises Émeraude accompagne les particuliers de la <strong>Côte d\'Émeraude</strong>, du <strong>pays malouin</strong> et de la <strong>métropole rennaise</strong> dans le choix et l\'installation de leur chauffage au bois. Nous sélectionnons des appareils performants, écologiques et économiques, issus des plus grandes marques européennes.</p><h3>Poêle à bois à Saint-Malo et Dinard</h3><p>Maisons de pierre, longères, constructions récentes : nos équipes connaissent le bâti local et dimensionnent chaque <strong>poêle à bois</strong> ou <strong>insert</strong> selon votre surface et votre conduit.</p><h3>Poêle à granulés à Rennes</h3><p>Avec l\'ouverture de notre magasin rennais, nous installons désormais des <strong>poêles à granulés</strong> et des poêles mixtes dans toute la métropole, avec le même service d\'entretien et de ramonage.</p>']],
                ['type' => 'final', 'data' => ['hand' => 'On allume ?', 'title' => 'Parlons de votre futur *coin du feu*',
                    'cta_label' => 'Demander mon devis gratuit', 'cta_url' => '/devis/', 'cta2_label' => 'Passer en magasin', 'cta2_url' => '/#groupe']],
            ];
            $home->faq = [
                ['question' => 'Poêle à bois ou poêle à granulés : que choisir ?', 'answer' => 'Le poêle à bois offre une vraie flamme et un combustible bon marché si vous avez de la place pour stocker. Le poêle à granulés est programmable et autonome, idéal pour un chauffage principal. Les poêles mixtes acceptent les deux.'],
                ['question' => 'Le devis est-il gratuit ?', 'answer' => 'Oui. Un technicien vient chez vous faire un relevé technique, puis le devis vous est envoyé gratuitement et rapidement.'],
                ['question' => 'Quelles aides pour installer un poêle ?', 'answer' => 'Selon la réglementation en vigueur, l\'installation d\'un poêle performant peut ouvrir droit à des aides de l\'État et des collectivités. Nous vous indiquons celles auxquelles vous avez droit lors du devis.'],
                ['question' => 'Assurez-vous l\'entretien et le ramonage ?', 'answer' => 'Oui, nos équipes assurent l\'entretien et le ramonage de votre poêle ou insert dans nos trois secteurs : Côte d\'Émeraude, pays malouin et Rennes Métropole.'],
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
