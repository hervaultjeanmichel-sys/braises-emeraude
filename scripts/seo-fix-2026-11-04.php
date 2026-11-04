<?php

declare(strict_types=1);

/*
 * Correctifs SEO du 04/11/2026 (mise en ligne) — directement en base, sans passer par GitHub.
 *   php scripts/seo-fix-2026-11-04.php           → simulation (rien n'est modifié)
 *   php scripts/seo-fix-2026-11-04.php --apply   → applique
 *
 * 1. Retire le texte Divi « Your content goes here… » de tous les contenus.
 * 2. Titles : supprime le « - Braises Emeraude » hérité de WordPress (doublon avec le suffixe du site),
 *    complète les titles faibles ou trop longs.
 * 3. Descriptions : remplit les vides, réécrit les faibles ; fiches produits = description unique par produit
 *    (plus de texte copié-collé d'une fiche à l'autre).
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$apply = in_array('--apply', $argv, true);
$S = ' · Braises Émeraude';
$count = ['placeholder' => 0, 'title' => 0, 'description' => 0];
$warn = [];

$set = function (string $table, $id, array $data, string $label) use ($apply, &$count) {
    foreach ($data as $col => $v) {
        $count[$col === 'seo_title' ? 'title' : 'description']++;
    }
    echo str_pad($table, 18).' '.str_pad((string) $label, 55).' '.implode(' | ', array_map(fn ($k, $v) => $k.'('.mb_strlen((string) $v).')', array_keys($data), $data)).PHP_EOL;
    if ($apply) {
        DB::table($table)->where('id', $id)->update($data + ['updated_at' => now()]);
    }
};

// ─── 1. Texte Divi « Your content goes here » ────────────────────────────────
echo "=== 1. Texte Divi à retirer ===\n";
$textTables = ['pages' => ['content', 'intro'], 'products' => ['content', 'description', 'intro'], 'posts' => ['content', 'excerpt'],
    'stores' => ['content', 'intro'], 'cities' => ['content', 'intro'], 'brands' => ['content', 'intro'],
    'product_categories' => ['content', 'intro'], 'aid_topics' => ['content', 'intro']];
foreach ($textTables as $table => $cols) {
    if (! Schema::hasTable($table)) {
        continue;
    }
    foreach ($cols as $col) {
        if (! Schema::hasColumn($table, $col)) {
            continue;
        }
        foreach (DB::table($table)->where($col, 'like', '%Your content goes here%')->get(['id', $col]) as $row) {
            $html = (string) $row->$col;
            $new = preg_replace('#<(p|div)[^>]*>\s*Your content goes here.*?Advanced settings\.?\s*</\1>#isu', '', $html);
            $new = preg_replace('#Your content goes here.*?Advanced settings\.?#isu', '', (string) $new);
            $new = preg_replace('#<p>\s*</p>#', '', (string) $new);
            if ($new !== $html) {
                $count['placeholder']++;
                echo "  $table #$row->id ($col)\n";
                $apply && DB::table($table)->where('id', $row->id)->update([$col => $new]);
            }
        }
    }
}

// ─── 2. Suffixe WordPress dans les seo_title ─────────────────────────────────
echo "\n=== 2. Suffixe « - Braises Emeraude » retiré des titles ===\n";
$wpSuffix = '/\s*[-–|·]\s*Braises\s+[EÉ]meraudes?\s*$/iu';
foreach (['pages', 'products', 'posts', 'stores', 'cities', 'brands', 'product_categories', 'aid_topics'] as $table) {
    if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'seo_title')) {
        continue;
    }
    foreach (DB::table($table)->whereNotNull('seo_title')->get(['id', 'seo_title']) as $row) {
        $clean = trim((string) preg_replace($wpSuffix, '', (string) $row->seo_title));
        // « Poêle écologique à Rennes – Braises Émeraude - Braises Emeraude » : deux suffixes
        $clean = trim((string) preg_replace($wpSuffix, '', $clean));
        if ($clean !== $row->seo_title) {
            $set($table, $row->id, ['seo_title' => $clean], (string) $row->seo_title);
        }
    }
}

// ─── 3. Titles et descriptions rédigés (pages, magasins, villes, articles, marque) ─
echo "\n=== 3. Titles et descriptions rédigés ===\n";
$manual = [
    ['pages', '/', 'Poêle bois et granulés Saint-Malo Dinard Rennes'.$S, 'Poêles à bois, à granulés et inserts : 3 showrooms à Dinard, Saint-Malo et Rennes, pose et entretien. Visite technique et devis gratuits à domicile.'],
    ['pages', 'inserts', 'Insert à bois et à granulés pour cheminée'.$S, 'Insert à bois ou à granulés pour transformer votre cheminée en vrai chauffage. Visite technique gratuite et pose à Dinard, Saint-Malo et Rennes.'],
    ['pages', 'notre-entreprise', 'Notre entreprise, spécialiste du poêle'.$S, 'Braises Émeraude, spécialiste du chauffage au bois et aux granulés en Ille-et-Vilaine : 3 showrooms et des techniciens pour la pose et l\'entretien.'],
    ['pages', 'showroom', 'Nos showrooms à Dinard, Saint-Malo et Rennes'.$S, 'Nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes : poêles et inserts en fonctionnement. Visite technique et devis gratuits.'],
    ['pages', 'chauffage', 'Chauffage bois, granulés et mixte'.$S, 'Poêles et inserts à bois, à granulés ou mixtes : le chauffage adapté à votre maison. Conseil en showroom, visite technique gratuite et pose par nos équipes.'],
    ['pages', 'chauffage-granules', 'Chauffage aux granulés : poêles et inserts'.$S, 'Poêles et inserts à granulés programmables et connectés : confort automatique et faible consommation. Conseil, pose et entretien par Braises Émeraude.'],
    ['pages', 'chauffage-bois', 'Chauffage au bois : poêles, inserts, foyers'.$S, 'Poêles, inserts et foyers à bois : la vraie flamme et une chaleur douce. Large choix en showroom, visite technique gratuite et pose par nos équipes.'],
    ['pages', 'chauffage-mixte', 'Chauffage mixte bois et granulés'.$S, 'Poêle mixte bois et granulés : la flamme des bûches et le confort programmable des pellets. Conseil, pose et entretien par Braises Émeraude.'],
    ['pages', 'chauffage-gaz', 'Chauffage au gaz : poêles et inserts'.$S, null],
    ['pages', 'cuisson', 'Cuisinières à bois, barbecues, braseros'.$S, 'Cuisinières à bois Rizzoli, barbecues et braseros : chauffez et cuisinez au feu de bois. À découvrir dans nos showrooms de Dinard, Saint-Malo et Rennes.'],
    ['pages', 'boutique', 'Catalogue poêles, inserts et cuisinières'.$S, null],
    ['pages', 'nos-realisations', 'Nos réalisations : poêles et inserts posés'.$S, 'Poêles, inserts et foyers installés par nos équipes à Dinard, Saint-Malo, Rennes et alentours : découvrez nos chantiers en photos.'],
    ['pages', 'contact', 'Contact et rendez-vous technicien'.$S, 'Contactez Braises Émeraude à Dinard, Saint-Malo ou Rennes et prenez rendez-vous pour une visite technique gratuite à domicile, qui établit votre devis.'],
    ['pages', 'poele-ecologique-rennes', 'Poêle écologique à Rennes'.$S, 'Poêle écologique à Rennes : bois ou granulés, des appareils performants à faibles émissions. Visite technique gratuite, pose et entretien par nos équipes.'],
    ['pages', 'insert-cheminee-rennes', 'Insert de cheminée à Rennes'.$S, 'Insert de cheminée à Rennes : bois ou granulés, transformez votre foyer ouvert en vrai chauffage. Visite technique gratuite et pose par Braises Émeraude.'],
    ['stores', 'poele-a-bois-granules-saint-malo', 'Poêle à bois et granulés à Saint-Malo'.$S, 'Showroom de Baguer-Morvan pour Saint-Malo, Cancale et Dol : poêles à bois, à granulés et inserts. Visite technique gratuite à domicile, pose et entretien.'],
    ['stores', 'poele-a-bois-granules-rennes', 'Poêle à bois et granulés à Rennes'.$S, 'Poêles à bois, à granulés et inserts à Rennes et dans la métropole : showroom Braises Émeraude, visite technique gratuite à domicile, pose et entretien.'],
    ['cities', 'poele-a-bois-granules-cancale', 'Poêle à bois et granulés à Cancale'.$S, 'Poêle à bois ou à granulés à Cancale : conseil au showroom de Baguer-Morvan, visite technique gratuite à domicile, pose et entretien par nos équipes.'],
    ['brands', 'm-design', null, 'Foyers et poêles M Design, pour une cheminée au design affirmé. À découvrir chez Braises Émeraude à Dinard, Saint-Malo et Rennes : conseil et pose.'],
    ['posts', 'poele-a-bois-granules-a-cancale-35', 'Poêle à bois et granulés à Cancale (35)'.$S, 'Poêle à bois ou à granulés à Cancale (35) : quel appareil choisir, quelle puissance, quel budget ? Les conseils de Braises Émeraude, près de chez vous.'],
    ['posts', 'aides-financieres-poele-a-bois-granules-cancale', 'Aides poêle bois et granulés à Cancale'.$S, 'Aides pour un poêle à bois ou à granulés à Cancale en 2026 : prime CEE, TVA à 5,5 %, éco-PTZ. Ce qui reste possible depuis la fin de MaPrimeRénov\'.'],
    ['posts', 'blog-poele-bois-ou-granules-rennes', 'Bois ou granulés à Rennes : lequel choisir ?'.$S, 'Poêle à bois ou à granulés à Rennes : confort, budget, entretien, place. Nos conseils pour choisir l\'appareil adapté à votre logement.'],
    ['posts', 'blog-chauffage-bois-ecologique-saint-malo', 'Chauffage au bois écologique à Saint-Malo'.$S, 'Chauffage au bois écologique à Saint-Malo : pourquoi le bois reste une énergie locale et performante, et comment bien choisir votre poêle ou insert.'],
    ['posts', 'poele-bois-ou-granules-rennes-differences-quotidien', 'Bois ou granulés : différences au quotidien'.$S, 'Poêle à bois ou à granulés à Rennes : allumage, autonomie, entretien, stockage. Les vraies différences au quotidien pour bien choisir.'],
    ['product_categories', 'chauffage-bois/insert-chauffage-bois', 'Insert et foyer à bois pour cheminée'.$S, null],
    ['product_categories', 'chauffage-granule/poele', 'Poêle à granulés programmable'.$S, null],
    ['aid_topics', 'tva-5-5-poele', 'TVA 5,5 % poêle bois et granulés : conditions'.$S, null],
    ['aid_topics', 'eco-ptz-poele', 'Éco-PTZ pour financer votre poêle'.$S, null],
];
foreach ($manual as [$table, $slug, $title, $desc]) {
    if (! Schema::hasTable($table)) {
        continue;
    }
    $key = $table === 'product_categories' ? 'path' : 'slug';
    $row = DB::table($table)->where($key, $slug)->first(['id']);
    if (! $row) {
        $warn[] = "introuvable : $table $slug";
        continue;
    }
    $data = array_filter(['seo_title' => $title, 'seo_description' => $desc], fn ($v) => $v !== null);
    $set($table, $row->id, $data, $slug);
}
// Page /devis/ (réglages)
foreach (['devis.seo_title' => 'Devis poêle : visite technique gratuite'.$S,
    'devis.seo_description' => 'Demandez votre devis poêle ou insert : quelques questions en 2 minutes, puis un technicien vient gratuitement chez vous vérifier et chiffrer l\'installation.'] as $k => $v) {
    echo str_pad('settings', 18).' '.$k.' ('.mb_strlen($v).")\n";
    $apply && DB::table('settings')->updateOrInsert(['key' => $k], ['value' => $v, 'group' => 'devis', 'updated_at' => now()]);
}

// ─── 4. Fiches produits : title complété + description unique ────────────────
echo "\n=== 4. Fiches produits ===\n";
$labelFor = function (?string $path, string $name, ?string $energy): string {
    $n = mb_strtolower($name);
    if (str_contains($n, 'cuisini')) {
        return 'cuisinière à bois';
    }
    if (str_contains($n, 'foyer')) {
        return 'foyer à bois';
    }
    $map = [
        'chauffage-bois/insert-chauffage-bois' => 'insert à bois',
        'chauffage-bois/poele-chauffage-bois' => 'poêle à bois',
        'chauffage-bois' => 'poêle à bois',
        'chauffage-granule/insert' => 'insert à granulés',
        'chauffage-granule/poele' => 'poêle à granulés',
        'chauffage-granule' => 'poêle à granulés',
        'chauffage-mixte' => 'poêle mixte bois et granulés',
        'cuisson' => 'cuisinière à bois',
    ];
    if ($path && isset($map[$path])) {
        return $map[$path];
    }

    return match ($energy) {
        'granules' => 'poêle à granulés', 'mixte' => 'poêle mixte bois et granulés', default => 'poêle à bois',
    };
};
$nice = function (string $name): string {
    $words = preg_split('/\s+/u', trim($name));
    $words = array_map(function (string $w) {
        if (preg_match('/\d/', $w) || mb_strlen($w) <= 3 || $w !== mb_strtoupper($w)) {
            return $w; // références (SP1, 2.0, N°6, WD, MCZ) et mots déjà en casse mixte
        }

        return mb_convert_case(mb_strtolower($w), MB_CASE_TITLE);
    }, $words);

    return implode(' ', $words);
};

$products = DB::table('products as p')
    ->leftJoin('product_categories as c', 'c.id', '=', 'p.product_category_id')
    ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
    ->where('p.is_published', true)
    ->get(['p.id', 'p.slug', 'p.name', 'p.seo_title', 'p.seo_description', 'p.energy', 'p.power_kw', 'c.path', 'b.name as brand']);
$dupes = $products->pluck('seo_description')->filter()->countBy();

foreach ($products as $p) {
    $name = $nice((string) $p->name);
    if ($p->brand && ! str_contains(mb_strtolower($name), mb_strtolower($p->brand))) {
        $name .= ' '.$p->brand;
    }
    $label = $labelFor($p->path, $name, $p->energy);
    $hasType = (bool) preg_match('/po[eê]le|insert|foyer|cuisini/iu', $name);
    $title = $hasType || mb_strlen($name.' : '.$label) > 46 ? $name : $name.' : '.$label;
    $title .= $S;

    $data = [];
    $current = trim((string) preg_replace($wpSuffix, '', (string) $p->seo_title));
    if ($current === '' || $current === $p->name || mb_strtoupper($current) === mb_strtoupper((string) $p->name)) {
        $data['seo_title'] = $title;
    }

    $desc = (string) $p->seo_description;
    $len = mb_strlen($desc);
    if ($desc === '' || ($dupes[$desc] ?? 0) > 1 || $len < 110 || $len > 160) {
        $kw = $p->power_kw ? ' de '.rtrim(rtrim(number_format((float) $p->power_kw, 1, ',', ''), '0'), ',').' kW' : '';
        $intro = $hasType ? $name.$kw : $name.', '.$label.$kw;
        $tail = '. À voir en showroom à Dinard, Saint-Malo et Rennes. Visite technique et devis gratuits à domicile.';
        $new = $intro.$tail;
        if (mb_strlen($new) > 160) {
            $new = $intro.'. En showroom à Dinard, Saint-Malo, Rennes. Visite technique et devis gratuits.';
        }
        if (mb_strlen($new) > 160) {
            $new = $name.'. En showroom à Dinard, Saint-Malo, Rennes. Visite technique et devis gratuits.';
        }
        $data['seo_description'] = $new;
    }
    if ($data) {
        $set('products', $p->id, $data, $p->slug);
    }
}

echo "\n".($apply ? 'APPLIQUÉ' : 'SIMULATION (relancer avec --apply)')." — textes Divi : {$count['placeholder']}, titles : {$count['title']}, descriptions : {$count['description']}\n";
foreach ($warn as $w) {
    echo "ATTENTION $w\n";
}
if ($apply) {
    cache()->flush();
    echo "Cache vidé.\n";
}
