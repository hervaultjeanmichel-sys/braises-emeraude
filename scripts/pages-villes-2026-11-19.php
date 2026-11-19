<?php

declare(strict_types=1);

/*
 * Pages villes (19/11/2026) — suite au contrôle Search Console J+15.
 *   php scripts/pages-villes-2026-11-19.php           → simulation
 *   php scripts/pages-villes-2026-11-19.php --apply   → applique
 *
 * - Dinard (page magasin) et Cancale (page ville) : sections AJOUTÉES après le texte existant
 *   (remplacement seulement si la page a moins de 60 mots). Marqueur <!-- be-villes-1911 --> : pas de doublon.
 * - Melesse et Rennes Nord : pages villes CRÉÉES, rattachées au magasin de Rennes (La Mézière).
 *   Si la page existe déjà, elle n'est pas modifiée.
 * Tout reste modifiable dans l'admin (Villes / Magasins). Règle : aucun fait non vérifié.
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$M = '<!-- be-villes-1911 -->';
$S = ' · Braises Émeraude';
$devis = '<p>Le devis se fait toujours <strong>chez vous</strong> : un technicien vient gratuitement contrôler la faisabilité (conduit, emplacement, ventilation) et chiffrer précisément l\'installation. <a href="/devis/">Demander votre visite technique gratuite</a>.</p>';
$devisRennes = '<p>Le devis se fait toujours <strong>chez vous</strong> : un technicien vient gratuitement contrôler la faisabilité (conduit, emplacement, ventilation) et chiffrer précisément l\'installation. <a href="/rendez-vous/">Prendre rendez-vous avec un technicien</a> ou <a href="/devis/">préparer votre demande en ligne</a>.</p>';

// ─────────────────────────────────────────── 1. Textes ajoutés aux pages existantes
$add = [];

$add['stores']['poele-a-bois-granules-dinard'] = <<<HTML
<h2>Poêle à granulés à Dinard : le confort automatique</h2>
<p>Le poêle à granulés est l'appareil le plus demandé sur la rive gauche de la Rance. Il s'allume et s'éteint selon vos horaires, maintient la température choisie et se pilote, selon les modèles, avec un thermostat, une télécommande ou une application. Pratique pour une résidence principale, il l'est aussi pour une maison secondaire : on peut relancer le chauffage à distance avant d'arriver.</p>
<h3>Air, canalisable ou hydro ?</h3>
<ul>
<li><strong>Le poêle à granulés à air</strong> chauffe la pièce où il est installé : idéal pour une pièce de vie ouverte.</li>
<li><strong>Le poêle à granulés canalisable</strong> envoie une partie de l'air chaud vers une ou deux pièces voisines par des gaines.</li>
<li><strong>Le poêle à granulés hydro</strong> se raccorde aux radiateurs ou au plancher chauffant et peut produire l'eau chaude.</li>
</ul>
<p>Le bon choix dépend de la surface, de la disposition des pièces et de l'isolation : c'est précisément ce que vérifie notre technicien lors de la visite à domicile.</p>
<h2>Le poêle mixte bois et granulés</h2>
<p>Le <a href="/categorie-produit/chauffage-mixte/">poêle mixte</a> fonctionne au choix avec des bûches ou des granulés. Les granulés tempèrent la maison en semaine, la flambée de bûches prend le relais le soir ou le week-end. Une solution souple pour les longues intersaisons de la Côte d'Émeraude.</p>
<h2>Installation d'un poêle à Dinard et alentour</h2>
<p>Nos équipes installent les appareils vendus au showroom de Dinard · Pleurtuit : création ou tubage du conduit, raccordement, mise en service et explications sur l'utilisation. Dans les maisons anciennes et les villas balnéaires, le conduit existant est vérifié avant tout projet ; en construction récente, la ventilation et l'arrivée d'air sont étudiées avec soin.</p>
<h3>Après la pose : l'entretien</h3>
<p>Un poêle à bois ou à granulés s'entretient chaque année, et le conduit doit être ramoné selon la réglementation et votre contrat d'assurance. Parlez-en à l'équipe du magasin lors de votre projet.</p>
$devis
HTML;

$add['cities']['poele-a-bois-granules-cancale'] = <<<HTML
<h2>Chauffer une maison à Cancale avec le bois ou les granulés</h2>
<p>Maisons de pêcheurs du port de la Houle, longères de la campagne cancalaise, maisons récentes en périphérie : à Cancale, les logements sont très différents et le poêle doit s'adapter à chacun. Exposition au vent, humidité marine, pièces aux volumes variés : notre équipe de Saint-Malo · Baguer-Morvan connaît ces contraintes et vous oriente vers l'appareil adapté.</p>
<h2>Quel appareil choisir ?</h2>
<h3>Le poêle à bois</h3>
<p>Pour la vraie flamme, le crépitement et une chaleur rayonnante. Les <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a> actuels brûlent beaucoup mieux que les anciennes cheminées ouvertes et consomment moins de bois.</p>
<h3>Le poêle à granulés</h3>
<p>Programmable et autonome grâce à son réservoir, le <a href="/categorie-produit/chauffage-granule/">poêle à granulés</a> maintient une température régulière sans intervention. Il existe en version air, canalisable (pour chauffer plusieurs pièces) ou hydro (raccordé aux radiateurs).</p>
<h3>L'insert</h3>
<p>Vous avez déjà une cheminée ? Un <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">insert</a> se glisse dans le foyer existant et transforme une cheminée peu efficace en véritable chauffage.</p>
<h2>Le showroom le plus proche de Cancale</h2>
<p>Les Cancalais sont accueillis à notre showroom de <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo · Baguer-Morvan</a>, notre plus grand magasin, où plusieurs appareils sont présentés en fonctionnement. Vous y trouverez aussi des granulés de bois.</p>
<h2>Les aides pour votre poêle</h2>
<p>Selon votre situation et l'appareil choisi, des aides peuvent réduire le coût de votre projet. Retrouvez le détail sur notre page <a href="/aides-financement/">aides et financement</a>, et posez vos questions lors de la visite technique.</p>
$devis
HTML;

$meta = [
    // table, slug, champs à compléter seulement s'ils sont vides
    ['cities', 'poele-a-bois-granules-cancale', [
        'neighbours' => json_encode(['Saint-Coulomb', 'Saint-Méloir-des-Ondes', 'La Gouesnière', 'Saint-Malo', 'Saint-Benoît-des-Ondes', 'Hirel'], JSON_UNESCAPED_UNICODE),
        'faq' => json_encode([
            ['question' => 'Quel showroom est le plus proche de Cancale ?', 'answer' => 'Notre showroom de Saint-Malo · Baguer-Morvan. Vous y découvrez les poêles à bois, à granulés et mixtes en fonctionnement.'],
            ['question' => 'Le devis est-il gratuit ?', 'answer' => 'Oui. Un technicien vient gratuitement chez vous à Cancale pour vérifier le conduit, l\'emplacement et la ventilation, puis établit un devis précis.'],
            ['question' => 'Peut-on installer un poêle dans une maison ancienne de Cancale ?', 'answer' => 'Dans la plupart des cas, oui. Le conduit existant est contrôlé lors de la visite technique ; s\'il ne convient pas, il peut être tubé ou un nouveau conduit peut être créé.'],
        ], JSON_UNESCAPED_UNICODE),
    ]],
];

// ─────────────────────────────────────────── 2. Nouvelles pages villes (magasin de Rennes)
$rennesId = DB::table('stores')->where('slug', 'poele-a-bois-granules-rennes')->value('id');

$new = [];

$new['poele-a-bois-granules-melesse'] = [
    'name' => 'Melesse',
    'postal_code' => '35520',
    'eyebrow' => 'Nord de Rennes · Melesse',
    'h1' => 'Poêle à bois et à granulés à Melesse',
    'intro' => 'Vous habitez Melesse ou ses environs ? Notre magasin de La Mézière, tout proche, vous présente poêles à bois, poêles à granulés, poêles mixtes et inserts. Conseil au showroom, visite technique gratuite à domicile, pose et mise en service par nos équipes.',
    'seo_title' => 'Poêle à bois et granulés à Melesse'.$S,
    'seo_description' => 'Poêle à bois, à granulés ou mixte à Melesse : showroom Braises Émeraude à La Mézière, visite technique gratuite à domicile, pose et mise en service.',
    'neighbours' => ['La Mézière', 'Montreuil-le-Gast', 'Saint-Germain-sur-Ille', 'Vignoc', 'Saint-Médard-sur-Ille', 'Gévezé', 'Betton', 'La Chapelle-des-Fougeretz'],
    'content' => <<<HTML
<h2>Votre magasin de poêles à La Mézière, à côté de Melesse</h2>
<p>Melesse et La Mézière sont voisines : notre magasin de La Mézière est donc le showroom Braises Émeraude le plus proche pour les Melessiens. Vous pouvez y voir les appareils, comparer les modèles et les finitions, et poser toutes vos questions à nos conseillers avant de lancer votre projet.</p>

<h2>Quel chauffage au bois pour votre maison à Melesse ?</h2>
<p>Bourg ancien, longères et corps de ferme rénovés, lotissements récents : à Melesse comme dans tout le nord de Rennes, les maisons sont variées. Le bon appareil dépend de la surface à chauffer, de l'isolation, de la disposition des pièces et de la présence (ou non) d'un conduit.</p>
<h3>Le poêle à bois</h3>
<p>La vraie flamme et la chaleur rayonnante des bûches. Les <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a> actuels offrent un rendement élevé et une combustion bien plus propre qu'une cheminée ouverte. Idéal si vous avez accès à du bois.</p>
<h3>Le poêle à granulés</h3>
<p>Programmable, régulé, autonome plusieurs heures grâce à son réservoir : le <a href="/categorie-produit/chauffage-granule/">poêle à granulés</a> chauffe la maison sans intervention. Version air pour la pièce de vie, canalisable pour chauffer aussi des pièces voisines, ou hydro pour alimenter les radiateurs.</p>
<h3>Le poêle mixte</h3>
<p>Le <a href="/categorie-produit/chauffage-mixte/">poêle mixte</a> accepte les bûches et les granulés : automatique en semaine, flambée le week-end.</p>
<h3>L'insert</h3>
<p>Vous avez une cheminée ? Un <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">insert</a> s'installe dans le foyer existant et le transforme en véritable chauffage.</p>

<h2>Votre projet, étape par étape</h2>
<h3>1. Le choix au showroom de La Mézière</h3>
<p>Vous découvrez les modèles et repérez ceux qui correspondent à votre maison et à vos envies.</p>
<h3>2. La visite technique chez vous, à Melesse</h3>
<p>Un technicien se déplace gratuitement : il vérifie le conduit, l'emplacement, les distances de sécurité et l'arrivée d'air, puis établit un devis précis.</p>
<h3>3. La pose et la mise en service</h3>
<p>Nos équipes installent l'appareil, le mettent en service et vous expliquent son utilisation et son entretien.</p>

<h2>Entretien et ramonage</h2>
<p>Un poêle s'entretient chaque année et le conduit doit être ramoné selon la réglementation et votre contrat d'assurance. Un appareil bien entretenu chauffe mieux, consomme moins et dure plus longtemps.</p>

<h2>Les aides pour votre poêle</h2>
<p>Selon votre situation et l'appareil choisi, des aides peuvent réduire le coût de votre projet. Retrouvez le détail sur notre page <a href="/aides-financement/">aides et financement</a>.</p>

<h2>Melesse et les communes voisines</h2>
<p>Nous accompagnons les habitants de Melesse et des communes voisines du nord de Rennes. Pour l'ensemble de la métropole, consultez aussi notre page <a href="/poele-a-bois-granules-rennes/">Rennes</a> et notre page <a href="/poele-a-bois-granules-rennes-nord/">nord de Rennes</a>.</p>
$devisRennes
HTML,
    'faq' => [
        ['question' => 'Quel magasin Braises Émeraude est le plus proche de Melesse ?', 'answer' => 'Notre magasin de La Mézière, commune voisine de Melesse. Vous y découvrez poêles à bois, à granulés, mixtes et inserts.'],
        ['question' => 'Le devis est-il gratuit ?', 'answer' => 'Oui. Un technicien vient gratuitement chez vous à Melesse pour vérifier la faisabilité de l\'installation et établir un devis précis.'],
        ['question' => 'Poêle à bois ou poêle à granulés : que choisir ?', 'answer' => 'Le poêle à bois offre la vraie flamme et convient si vous avez accès à du bois. Le poêle à granulés est programmable et autonome. Le poêle mixte accepte les deux. Nos conseillers vous aident à choisir selon votre maison et votre façon de vivre.'],
        ['question' => 'Faut-il un conduit existant pour installer un poêle ?', 'answer' => 'Non. Si la maison n\'a pas de conduit, un conduit peut être créé. S\'il en existe un, il est contrôlé lors de la visite technique et peut être tubé si nécessaire.'],
    ],
];

$new['poele-a-bois-granules-rennes-nord'] = [
    'name' => 'Rennes Nord',
    'postal_code' => null,
    'eyebrow' => 'Nord de Rennes',
    'h1' => 'Poêle à bois et à granulés au nord de Rennes',
    'intro' => 'Saint-Grégoire, Betton, Montgermont, La Chapelle-des-Fougeretz, Melesse, Gévezé… Pour tout le nord de Rennes, notre magasin de La Mézière présente poêles à bois, poêles à granulés, poêles mixtes et inserts. Visite technique gratuite à domicile, pose et mise en service par nos équipes.',
    'seo_title' => 'Poêle à bois et granulés au nord de Rennes'.$S,
    'seo_description' => 'Poêle à bois, à granulés ou mixte au nord de Rennes : Saint-Grégoire, Betton, Montgermont, Melesse… Showroom à La Mézière, visite technique gratuite à domicile.',
    'neighbours' => ['La Mézière', 'Melesse', 'Saint-Grégoire', 'Betton', 'Montgermont', 'La Chapelle-des-Fougeretz', 'Gévezé', 'Vignoc', 'Chevaigné', 'Saint-Sulpice-la-Forêt'],
    'content' => <<<HTML
<h2>Un showroom au nord de Rennes, à La Mézière</h2>
<p>Notre magasin de La Mézière est installé au nord de Rennes : pour les habitants de Saint-Grégoire, Betton, Montgermont, La Chapelle-des-Fougeretz, Melesse, Gévezé ou Vignoc, c'est le showroom Braises Émeraude le plus proche. Vous y découvrez les appareils, comparez les modèles et les finitions, et préparez votre projet avec nos conseillers.</p>

<h2>Poêles à bois, à granulés, mixtes et inserts</h2>
<h3>Le poêle à bois</h3>
<p>Pour la vraie flamme et la chaleur douce des bûches. Les <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a> actuels ont un rendement élevé et une combustion bien plus propre qu'une cheminée ouverte.</p>
<h3>Le poêle à granulés</h3>
<p>Le <a href="/categorie-produit/chauffage-granule/">poêle à granulés</a> s'allume et s'éteint selon vos horaires et maintient la température choisie. Il existe en version air, canalisable (pour chauffer plusieurs pièces) ou hydro (raccordé aux radiateurs ou au plancher chauffant).</p>
<h3>Le poêle mixte</h3>
<p>Le <a href="/categorie-produit/chauffage-mixte/">poêle mixte</a> fonctionne au choix avec des bûches ou des granulés : le confort de l'automatique et le plaisir de la flambée.</p>
<h3>L'insert</h3>
<p>Un <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">insert</a> se glisse dans une cheminée existante et la transforme en véritable chauffage.</p>

<h2>Des maisons différentes, un conseil sur mesure</h2>
<p>Maisons de bourg, longères rénovées, pavillons des années 1970 à 2000, constructions récentes très isolées : au nord de Rennes, chaque maison demande une réponse adaptée. Dans une maison récente, un appareil de faible puissance suffit souvent et la gestion de l'arrivée d'air est essentielle ; dans une maison plus ancienne, on cherche plutôt à chauffer plusieurs pièces. Nos conseillers vous orientent vers l'appareil et la puissance adaptés.</p>

<h2>Votre projet, étape par étape</h2>
<h3>1. Le choix au showroom</h3>
<p>Vous découvrez les modèles au magasin de La Mézière et repérez ceux qui vous plaisent.</p>
<h3>2. La visite technique chez vous</h3>
<p>Un technicien se déplace gratuitement : conduit, emplacement, distances de sécurité, ventilation. Il établit ensuite un devis précis.</p>
<h3>3. La pose et la mise en service</h3>
<p>Nos équipes installent l'appareil, le mettent en service et vous expliquent son utilisation et son entretien.</p>

<h2>Entretien, ramonage et aides</h2>
<p>Un poêle s'entretient chaque année et le conduit se ramone selon la réglementation et votre contrat d'assurance. Pour réduire le coût de votre projet, consultez notre page <a href="/aides-financement/">aides et financement</a>.</p>

<h2>Et pour le reste de la métropole ?</h2>
<p>Retrouvez toutes les informations sur notre magasin et la métropole sur la page <a href="/poele-a-bois-granules-rennes/">poêle à bois et granulés à Rennes</a>. Vous habitez Melesse ? Consultez aussi notre page <a href="/poele-a-bois-granules-melesse/">Melesse</a>.</p>
$devisRennes
HTML,
    'faq' => [
        ['question' => 'Où se trouve votre magasin au nord de Rennes ?', 'answer' => 'À La Mézière. C\'est notre showroom le plus proche pour Saint-Grégoire, Betton, Montgermont, La Chapelle-des-Fougeretz, Melesse, Gévezé et Vignoc.'],
        ['question' => 'Le devis est-il gratuit ?', 'answer' => 'Oui. Un technicien se déplace gratuitement chez vous pour vérifier la faisabilité de l\'installation et établir un devis précis.'],
        ['question' => 'Quel poêle pour une maison récente bien isolée ?', 'answer' => 'Souvent un appareil de faible puissance, avec une arrivée d\'air bien étudiée. Nos conseillers dimensionnent l\'appareil selon la surface et l\'isolation de votre maison.'],
        ['question' => 'Peut-on chauffer plusieurs pièces avec un poêle à granulés ?', 'answer' => 'Oui, avec un poêle à granulés canalisable, qui envoie l\'air chaud vers des pièces voisines, ou un poêle hydro raccordé aux radiateurs.'],
    ],
];

// ─────────────────────────────────────────── Exécution
$words = fn (?string $h) => str_word_count(strip_tags(html_entity_decode((string) $h)), 0, 'àâäéèêëîïôöùûüçœÀÂÉÈÊÎÔÙÛÇ');

foreach ($add as $table => $list) {
    foreach ($list as $slug => $html) {
        $row = DB::table($table)->where('slug', $slug)->first(['id', 'content']);
        if (! $row) {
            echo "ATTENTION introuvable : $table $slug\n";
            continue;
        }
        $current = (string) $row->content;
        if (str_contains($current, $M)) {
            echo str_pad("$table $slug", 50)." déjà fait\n";
            continue;
        }
        $mode = $words($current) < 60 ? 'remplacé' : 'complété';
        $newHtml = $mode === 'remplacé' ? $M."\n".$html : rtrim($current)."\n".$M."\n".$html;
        echo str_pad("$table $slug", 50).' '.str_pad($mode, 9).' '.$words($current).' → '.$words($newHtml)." mots\n";
        if ($apply) {
            @mkdir($root.'/storage/app/backup-villes-1911', 0755, true);
            file_put_contents($root."/storage/app/backup-villes-1911/$table-$slug.html", $current);
            DB::table($table)->where('id', $row->id)->update(['content' => $newHtml, 'updated_at' => now()]);
        }
    }
}

foreach ($meta as [$table, $slug, $fields]) {
    $row = DB::table($table)->where('slug', $slug)->first();
    if (! $row) {
        continue;
    }
    $set = array_filter($fields, fn ($v, $k) => empty($row->$k) || $row->$k === '[]' || $row->$k === 'null', ARRAY_FILTER_USE_BOTH);
    echo str_pad("$table $slug", 50).' champs complétés : '.($set ? implode(', ', array_keys($set)) : 'aucun (déjà remplis)')."\n";
    if ($apply && $set) {
        DB::table($table)->where('id', $row->id)->update($set + ['updated_at' => now()]);
    }
}

if (! $rennesId) {
    echo "ATTENTION magasin de Rennes introuvable : pages Melesse / Rennes Nord non créées\n";
} else {
    foreach ($new as $slug => $c) {
        if (DB::table('cities')->where('slug', $slug)->exists()) {
            echo str_pad("cities $slug", 50)." existe déjà, non modifiée\n";
            continue;
        }
        echo str_pad("cities $slug", 50).' créée      '.$words($c['content'])." mots, FAQ ".count($c['faq'])."\n";
        if ($apply) {
            DB::table('cities')->insert([
                'store_id' => $rennesId, 'name' => $c['name'], 'slug' => $slug, 'postal_code' => $c['postal_code'],
                'neighbours' => json_encode($c['neighbours'], JSON_UNESCAPED_UNICODE),
                'eyebrow' => $c['eyebrow'], 'h1' => $c['h1'], 'intro' => $c['intro'],
                'content' => $M."\n".$c['content'], 'faq' => json_encode($c['faq'], JSON_UNESCAPED_UNICODE),
                'seo_title' => $c['seo_title'], 'seo_description' => $c['seo_description'],
                'robots' => 'index,follow', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}

echo $apply ? "APPLIQUÉ\n" : "SIMULATION (relancer avec --apply)\n";
$apply && cache()->flush();
