<?php

declare(strict_types=1);

/*
 * Pages « ville + type de produit » (19/11/2026) : Poêle à granulés à Dinard, Poêle à granulés à Saint-Malo.
 *   php scripts/pages-ville-produit-2026-11-19.php           → simulation
 *   php scripts/pages-ville-produit-2026-11-19.php --apply   → applique
 * Nécessite la migration 2026_11_19_000100_city_product_pages (colonne cities.product_category_id).
 * Page existante = non modifiée. Tout reste modifiable dans l'admin (Villes). Règle : aucun fait non vérifié.
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$apply = in_array('--apply', $argv, true);
$M = '<!-- be-ville-produit-1911 -->';
$S = ' · Braises Émeraude';
$devis = '<p>Le devis se fait toujours <strong>chez vous</strong> : un technicien vient gratuitement vérifier l\'emplacement, le conduit, l\'arrivée d\'air et la prise électrique, puis chiffre précisément l\'installation. <a href="/devis/">Demander votre visite technique gratuite</a>.</p>';

if (! Schema::hasColumn('cities', 'product_category_id')) {
    exit("ARRÊT : lancer d'abord php artisan migrate --force\n");
}

$cat = DB::table('product_categories')->where('path', 'chauffage-granule/poele')->first(['id', 'name'])
    ?? DB::table('product_categories')->where('path', 'chauffage-granule')->first(['id', 'name']);
echo 'Catégorie : '.($cat ? "{$cat->name} (id {$cat->id})" : 'INTROUVABLE')."\n";
if (! $cat) {
    exit("ARRÊT : catégorie granulés introuvable\n");
}

$store = fn (string $slug) => DB::table('stores')->where('slug', $slug)->value('id');

$pages = [];

$pages['poele-a-granules-dinard'] = [
    'store' => 'poele-a-bois-granules-dinard',
    'name' => 'Poêle à granulés',
    'postal_code' => '35800',
    'eyebrow' => 'Dinard · Côte d\'Émeraude',
    'h1' => 'Poêle à granulés à Dinard',
    'intro' => 'Chauffage programmable, chaleur régulière, pilotage à distance : le poêle à granulés est l\'appareil le plus demandé sur la rive gauche de la Rance. Notre showroom de Dinard · Pleurtuit vous aide à choisir le bon modèle, puis nos équipes l\'installent chez vous.',
    'seo_title' => 'Poêle à granulés à Dinard : choix, pose, entretien'.$S,
    'seo_description' => 'Poêle à granulés à Dinard : air, canalisable ou hydro, modèles exposés au showroom de Dinard · Pleurtuit, visite technique gratuite, pose et entretien.',
    'neighbours' => ['Pleurtuit', 'Saint-Lunaire', 'Saint-Briac-sur-Mer', 'La Richardais', 'Le Minihic-sur-Rance', 'Lancieux'],
    'content' => <<<HTML
<h2>Pourquoi un poêle à granulés à Dinard ?</h2>
<p>Sur la Côte d'Émeraude, l'hiver est doux mais humide et venté, et la saison de chauffe s'étire de l'automne au printemps. Le poêle à granulés répond bien à ce climat : il maintient une température stable en continu, sans les à-coups d'une flambée, et sèche l'air de la maison.</p>
<p>Il s'allume et s'éteint tout seul selon vos horaires. Selon les modèles, il se pilote avec un thermostat, une télécommande ou une application sur smartphone. Pour une maison secondaire à Dinard ou Saint-Lunaire, c'est un vrai confort : on peut lancer le chauffage quelques heures avant d'arriver.</p>

<h2>Bien choisir son poêle à granulés</h2>
<h3>La puissance</h3>
<p>Plus puissant ne veut pas dire mieux. Un poêle surdimensionné tourne au ralenti, s'encrasse plus vite et consomme davantage. La bonne puissance dépend du volume à chauffer, de l'isolation, de l'exposition et de la hauteur sous plafond : une villa balnéaire aux grandes pièces n'a pas les mêmes besoins qu'une maison récente bien isolée.</p>
<h3>Le type de diffusion</h3>
<ul>
<li><strong>À air (convection)</strong> : un ventilateur diffuse la chaleur dans la pièce. Simple et efficace pour une pièce de vie ouverte.</li>
<li><strong>Étanche</strong> : l'air de combustion est pris directement à l'extérieur. Recommandé dans les maisons récentes très isolées, avec ventilation mécanique.</li>
<li><strong>Canalisable</strong> : des gaines emmènent une partie de l'air chaud vers une ou deux pièces voisines, une chambre ou un bureau par exemple.</li>
<li><strong>Hydro</strong> : le poêle chauffe l'eau des radiateurs ou du plancher chauffant, et peut produire l'eau chaude.</li>
</ul>
<h3>Le bruit et l'autonomie</h3>
<p>Le ventilateur et la vis d'alimentation font un léger bruit de fonctionnement, plus ou moins discret selon les modèles : venez l'écouter au showroom. La taille du réservoir fixe l'autonomie entre deux remplissages.</p>

<h2>Les granulés : qualité et stockage</h2>
<p>Un bon granulé fait un bon chauffage. Préférez des granulés certifiés (labels DINplus ou ENplus, par exemple) : peu de cendres, moins d'encrassement, un rendement régulier. Stockez les sacs au sec, à l'abri de l'humidité marine : un garage ou un cellier ventilé convient.</p>

<h2>Installation à Dinard et sur la rive gauche de la Rance</h2>
<p>Un poêle à granulés a besoin d'une prise électrique et d'un conduit d'évacuation adapté. Dans une maison ancienne, le conduit existant est contrôlé et peut être tubé ; sans conduit, une sortie en façade ou en toiture peut être créée selon la configuration. Notre technicien vérifie tout cela lors de la visite à domicile, avant le devis.</p>

<h2>Entretien</h2>
<p>Au quotidien, il suffit de vider le cendrier et de nettoyer le brasier régulièrement. Chaque année, un entretien complet par un professionnel et le ramonage du conduit sont nécessaires, selon la réglementation et votre contrat d'assurance.</p>

<h2>Voir les poêles à granulés en fonctionnement</h2>
<p>Plusieurs modèles sont exposés à notre showroom de <a href="/poele-a-bois-granules-dinard/">Dinard · Pleurtuit</a>. Pour comparer avec le bois ou le poêle mixte, consultez aussi nos <a href="/categorie-produit/chauffage-mixte/">poêles mixtes</a> et les <a href="/aides-financement/">aides et financement</a>.</p>
$devis
HTML,
    'faq' => [
        ['question' => 'Quel poêle à granulés pour une maison secondaire à Dinard ?', 'answer' => 'Un modèle programmable et pilotable à distance (application ou module connecté) permet de lancer le chauffage avant d\'arriver. La puissance se choisit selon le volume et l\'isolation de la maison.'],
        ['question' => 'Peut-on chauffer plusieurs pièces avec un poêle à granulés ?', 'answer' => 'Oui, avec un poêle canalisable qui envoie l\'air chaud vers des pièces voisines, ou avec un poêle hydro raccordé aux radiateurs.'],
        ['question' => 'Faut-il un conduit de cheminée ?', 'answer' => 'Il faut un conduit d\'évacuation adapté. Un conduit existant peut souvent être tubé ; sinon, une sortie peut être créée selon la configuration de la maison. Le technicien le vérifie lors de la visite gratuite.'],
        ['question' => 'Où voir des poêles à granulés près de Dinard ?', 'answer' => 'À notre showroom de Dinard · Pleurtuit, où plusieurs modèles sont exposés.'],
    ],
];

$pages['poele-a-granules-saint-malo'] = [
    'store' => 'poele-a-bois-granules-saint-malo',
    'name' => 'Poêle à granulés',
    'postal_code' => '35400',
    'eyebrow' => 'Saint-Malo · Pays malouin',
    'h1' => 'Poêle à granulés à Saint-Malo',
    'intro' => 'Pour Saint-Malo, Paramé, Saint-Servan, Saint-Jouan-des-Guérets et tout le pays malouin, notre showroom de Saint-Malo · Baguer-Morvan présente des poêles à granulés à air, canalisables et hydro. Conseil, visite technique gratuite, pose et entretien.',
    'seo_title' => 'Poêle à granulés à Saint-Malo : conseil et pose'.$S,
    'seo_description' => 'Poêle à granulés à Saint-Malo : modèles à air, canalisables et hydro au showroom de Baguer-Morvan, granulés sur place, visite technique gratuite et pose.',
    'neighbours' => ['Saint-Jouan-des-Guérets', 'Saint-Méloir-des-Ondes', 'Saint-Coulomb', 'Cancale', 'La Gouesnière', 'Châteauneuf-d\'Ille-et-Vilaine', 'Dol-de-Bretagne'],
    'content' => <<<HTML
<h2>Le poêle à granulés, un chauffage adapté aux maisons malouines</h2>
<p>Maisons de ville de Saint-Servan, villas de Paramé et de Rothéneuf, maisons des années 60 à 80, constructions récentes autour de Saint-Malo : le parc est très varié, et le poêle à granulés s'adapte à presque toutes les situations. Programmable, il chauffe à heures fixes, maintient la température choisie et demande peu d'intervention.</p>

<h2>Quel modèle pour quelle maison ?</h2>
<h3>Petite maison ou appartement en rez-de-jardin</h3>
<p>Un poêle compact de faible puissance suffit souvent. Les modèles « slim », peu profonds, se logent dans un couloir ou une pièce étroite.</p>
<h3>Maison sur plusieurs niveaux</h3>
<p>Dans une maison malouine typique, avec étage et pièces cloisonnées, un poêle canalisable permet d'envoyer de l'air chaud vers une chambre ou un bureau. Pour chauffer toute la maison, le poêle hydro se raccorde aux radiateurs existants.</p>
<h3>Maison récente bien isolée</h3>
<p>Ici, la priorité est une puissance modérée et un appareil étanche, qui prend son air de combustion à l'extérieur sans perturber la ventilation.</p>

<h2>Des granulés disponibles au magasin</h2>
<p>Notre showroom de <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo · Baguer-Morvan</a> est aussi un point de vente de granulés : vous repartez avec vos sacs en même temps que vos conseils. Choisissez de préférence des granulés certifiés (DINplus ou ENplus, par exemple) et stockez-les au sec.</p>

<h2>L'installation, étape par étape</h2>
<h3>1. Le choix au showroom</h3>
<p>Vous comparez les modèles, les finitions et le niveau de bruit des appareils exposés.</p>
<h3>2. La visite technique chez vous</h3>
<p>Le technicien contrôle le conduit (ou la possibilité d'en créer un), l'emplacement, la prise électrique et l'arrivée d'air. En centre-ville et en copropriété, il vérifie aussi les contraintes de sortie du conduit.</p>
<h3>3. La pose et la mise en service</h3>
<p>Nos équipes installent le poêle, règlent la programmation avec vous et vous montrent l'entretien courant.</p>

<h2>Entretien et ramonage</h2>
<p>Le brasier et le cendrier se nettoient régulièrement ; un entretien complet par un professionnel et le ramonage du conduit sont à prévoir chaque année, selon la réglementation et votre assurance.</p>

<h2>Comparer avant de choisir</h2>
<p>Vous hésitez avec le bois ? Découvrez nos <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a> et nos <a href="/categorie-produit/chauffage-mixte/">poêles mixtes</a>, et consultez les <a href="/aides-financement/">aides et financement</a> pour votre projet.</p>
$devis
HTML,
    'faq' => [
        ['question' => 'Où acheter des granulés près de Saint-Malo ?', 'answer' => 'Notre showroom de Saint-Malo · Baguer-Morvan vend des granulés sur place.'],
        ['question' => 'Peut-on installer un poêle à granulés dans une maison de ville à Saint-Malo ?', 'answer' => 'Souvent oui, avec un modèle compact et un conduit adapté. Les contraintes de sortie du conduit (copropriété, voisinage) sont vérifiées lors de la visite technique gratuite.'],
        ['question' => 'Poêle à granulés canalisable ou hydro ?', 'answer' => 'Le canalisable chauffe une ou deux pièces voisines par des gaines d\'air chaud. L\'hydro se raccorde aux radiateurs ou au plancher chauffant pour chauffer toute la maison.'],
        ['question' => 'Le devis est-il gratuit ?', 'answer' => 'Oui. Un technicien vient gratuitement chez vous pour vérifier la faisabilité et chiffrer l\'installation.'],
    ],
];

$words = fn (?string $h) => str_word_count(strip_tags(html_entity_decode((string) $h)), 0, 'àâäéèêëîïôöùûüçœÀÂÉÈÊÎÔÙÛÇ');

foreach ($pages as $slug => $c) {
    $storeId = $store($c['store']);
    if (! $storeId) {
        echo "ATTENTION magasin introuvable : {$c['store']}\n";
        continue;
    }
    if (DB::table('cities')->where('slug', $slug)->exists() || DB::table('pages')->where('slug', $slug)->exists() || DB::table('stores')->where('slug', $slug)->exists()) {
        echo str_pad($slug, 32)." existe déjà, non modifiée\n";
        continue;
    }
    echo str_pad($slug, 32).' créée  '.$words($c['content']).' mots, FAQ '.count($c['faq'])."\n";
    if ($apply) {
        DB::table('cities')->insert([
            'store_id' => $storeId, 'product_category_id' => $cat->id, 'name' => $c['name'], 'slug' => $slug, 'postal_code' => $c['postal_code'],
            'neighbours' => json_encode($c['neighbours'], JSON_UNESCAPED_UNICODE),
            'eyebrow' => $c['eyebrow'], 'h1' => $c['h1'], 'intro' => $c['intro'],
            'content' => $M."\n".$c['content'], 'faq' => json_encode($c['faq'], JSON_UNESCAPED_UNICODE),
            'seo_title' => $c['seo_title'], 'seo_description' => $c['seo_description'],
            'robots' => 'index,follow', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}

echo $apply ? "APPLIQUÉ\n" : "SIMULATION (relancer avec --apply)\n";
$apply && cache()->flush();
