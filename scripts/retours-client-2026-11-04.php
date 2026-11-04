<?php

declare(strict_types=1);

/*
 * Retours client du 04/11/2026 (PDF « nouveau site braisesemeraude ») — textes en base.
 *   php scripts/retours-client-2026-11-04.php           → simulation
 *   php scripts/retours-client-2026-11-04.php --apply   → applique
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$say = fn (string $m) => print($m.PHP_EOL);
$page = fn (string $slug) => DB::table('pages')->where('slug', $slug)->first();
$save = function (string $table, int $id, array $data) use ($apply) {
    $apply && DB::table($table)->where('id', $id)->update($data + ['updated_at' => now()]);
};

// 1. Texte au-dessus du bouton « Devis » (intro de l'en-tête) : /chauffage/ et /nos-realisations/
$intros = [
    'chauffage' => 'Poêle à bois, à granulés ou mixte, insert pour votre cheminée : comparez les solutions, venez les voir en fonctionnement dans nos showrooms, puis un technicien vient gratuitement chez vous établir votre devis.',
    'nos-realisations' => 'Poêles, inserts et foyers posés par nos équipes à Dinard, Saint-Malo, Rennes et alentours. Votre projet sera le prochain : un technicien vient chez vous pour un devis gratuit.',
];
foreach ($intros as $slug => $intro) {
    $p = $page($slug);
    if (! $p) {
        $say("ATTENTION page introuvable : $slug");
        continue;
    }
    $old = trim(strip_tags((string) $p->intro));
    $say("intro /$slug/ : ".($old === '' ? 'vide → ajoutée' : 'remplacée (ancienne : « '.mb_substr($old, 0, 80).'… »)'));
    $save('pages', $p->id, ['intro' => $intro]);
}

// 2. Contact : « Contactez-nous » + « Dès maintenant » sur une ligne ; trois showrooms
if ($p = $page('contact')) {
    $html = (string) $p->content;
    $new = preg_replace('#<(h[1-6])([^>]*)>\s*Contactez-nous\s*</\1>\s*<(h[1-6])[^>]*>\s*D[èe]s maintenant\s*(?:&nbsp;)?\s*\x21?\s*</\3>#iu', '<$1$2>Contactez-nous dès maintenant !</$1>', $html, -1, $n1);
    $new = preg_replace('#\bdeux showrooms#iu', 'trois showrooms', (string) $new, -1, $n2);
    $say("contact : titre fusionné ×$n1, « deux showrooms » → « trois showrooms » ×$n2".($n1 === 0 ? ' — ATTENTION titre non trouvé, à corriger dans l\'admin' : ''));
    if ($new !== $html) {
        $save('pages', $p->id, ['content' => $new]);
    }
}

// 3. /inserts/ : mots forts en couleur (même texte, mise en valeur)
$inserts = <<<'HTML'
<h2>Pourquoi installer un insert dans votre cheminée ?</h2>
<p>Une cheminée ouverte est agréable à regarder, mais <strong>l'essentiel de la chaleur part par le conduit</strong>. L'insert ferme le foyer avec une porte vitrée : <strong>la flamme reste visible</strong>, la combustion est maîtrisée et <strong>la chaleur reste dans la pièce</strong>. Vous consommez moins de combustible pour un confort bien supérieur, avec moins de fumée et d'escarbilles.</p>

<h2>Insert à bois ou insert à granulés ?</h2>
<h3>L'insert à bois</h3>
<p>Pour le plaisir de <strong>la vraie flambée</strong> et des bûches qui crépitent. Simple, <strong>sans électricité</strong>, il convient à ceux qui ont un accès facile au bois de chauffage et aiment entretenir le feu.</p>
<h3>L'insert à granulés</h3>
<p>Pour <strong>le confort au quotidien</strong> : il s'allume et se règle comme un chauffage, souvent avec un <strong>thermostat et une programmation</strong>. Le réservoir assure plusieurs heures d'autonomie ; il demande en revanche un branchement électrique.</p>

<h2>Une pose adaptée à votre cheminée</h2>
<p>Chaque cheminée est différente : dimensions du foyer, état du conduit, habillage existant. Avant tout devis, <strong>un technicien vient vérifier ces points chez vous</strong>, pour choisir un insert qui s'intègre proprement et <strong>chauffe à la bonne puissance</strong>. Le tubage du conduit et les raccordements font partie de l'étude.</p>

<h2>Voir les inserts en vrai</h2>
<p>Plusieurs modèles sont exposés dans <strong>nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes</strong>. Venez comparer les finitions, la taille des vitres et le fonctionnement, puis demandez votre <strong>visite technique gratuite</strong>.</p>
HTML;
if (($p = $page('inserts')) && str_contains((string) $p->content, 'Pourquoi installer un insert dans votre cheminée')) {
    $say('/inserts/ : texte mis en valeur (mots forts en couleur)');
    $save('pages', $p->id, ['content' => $inserts]);
} else {
    $say('/inserts/ : texte modifié dans l\'admin depuis sa création → non touché');
}

// 4. /cuisson/ : même contenu, plus percutant
$cuisson = <<<'HTML'
<!-- be-txt-0411 -->
<p class="lead"><strong>Le feu ne sert pas qu'à chauffer : il cuisine aussi.</strong> Mijotés, pains, gratins, grillades… Chez Braises Émeraude, le feu de bois se vit dedans l'hiver et dehors l'été.</p>
<h2>La cuisinière à bois : <strong>chauffer et cuisiner</strong> avec le même feu</h2>
<p>Une plaque de cuisson, un four, et <strong>une vraie source de chaleur</strong> pour toute la pièce de vie. La chaleur douce et régulière du bois donne <strong>des cuissons incomparables</strong>.</p>
<h3>Les cuisinières Rizzoli</h3>
<p>Plusieurs séries (L, M, N, R, S, ZVI) selon <strong>la taille du foyer, du four et le style</strong> recherché. <a href="/marque/rizzoli/">Découvrir la gamme Rizzoli</a>.</p>
<h3>Les poêles avec four</h3>
<p>Certains poêles à bois ou mixtes intègrent un compartiment de cuisson : <strong>la solution compacte</strong> pour cuire au four sans cuisinière complète.</p>
<h2>Barbecues et braseros : <strong>le feu dehors</strong></h2>
<p>Le brasero crée <strong>un point de convivialité</strong> autour du feu ; le barbecue au bois ou au charbon de bois donne aux grillades <strong>leur goût fumé</strong>. Nos conseillers vous aident à choisir le modèle adapté à votre terrasse.</p>
<h2>Une installation <strong>sûre</strong></h2>
<p>Une cuisinière à bois se raccorde à un conduit comme un poêle : conduit, distances de sécurité, arrivée d'air. <strong>Un technicien vérifie tout chez vous</strong> avant le devis, puis nos équipes s'occupent de la pose.</p>
<p>Le devis se fait toujours <strong>chez vous</strong> : un technicien vient gratuitement contrôler la faisabilité et chiffrer précisément l'installation. <a href="/rendez-vous/">Prendre rendez-vous avec un technicien</a> ou <a href="/devis/">préparer votre demande en ligne</a>.</p>
HTML;
if (($p = $page('cuisson')) && str_contains((string) $p->content, 'Cuisiner au feu de bois toute l')) {
    $say('/cuisson/ : texte rendu plus percutant (même contenu)');
    $save('pages', $p->id, ['content' => $cuisson]);
} else {
    $say('/cuisson/ : texte modifié dans l\'admin depuis le 04/11 → non touché');
}

// 5. Volet promo « ouverture » : logo MCZ
$mcz = DB::table('brands')->where('slug', 'mcz')->first(['id', 'logo']);
$promos = DB::table('promo_slides')->where(function ($q) {
    foreach (['name', 'title', 'text', 'eyebrow'] as $c) {
        $q->orWhere($c, 'like', '%ouverture%');
    }
})->get(['id', 'name']);
foreach ($promos as $pr) {
    $say("volet promo « {$pr->name} » : logo MCZ".($mcz && $mcz->logo ? '' : ' (aucun logo MCZ enregistré : le nom MCZ s\'affiche en pastille — déposer le logo dans Catalogue › Marques › MCZ)'));
    $mcz && $save('promo_slides', $pr->id, ['brand_id' => $mcz->id]);
}
$promos->isEmpty() && $say('ATTENTION aucun volet promo contenant « ouverture » : choisir la marque MCZ dans Contenus › Volets promo');

$say($apply ? 'APPLIQUÉ' : 'SIMULATION (relancer avec --apply)');
$apply && cache()->flush();
