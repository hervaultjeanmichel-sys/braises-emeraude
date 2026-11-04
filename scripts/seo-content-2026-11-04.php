<?php

declare(strict_types=1);

/*
 * Textes des pages vides ou trop courtes (04/11/2026), en remplacement du texte anglais Divi.
 *   php scripts/seo-content-2026-11-04.php           → simulation
 *   php scripts/seo-content-2026-11-04.php --apply   → applique
 * Page quasi vide (< 60 mots) : le texte remplace le contenu. Sinon : les sections sont AJOUTÉES après le texte existant.
 * Marqueur <!-- be-txt-0411 --> : relancer le script ne crée pas de doublon. Tout reste modifiable dans l'admin.
 * Règle : aucun fait non vérifié (pas de label, prix, origine de marque incertaine).
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$apply = in_array('--apply', $argv, true);
$M = '<!-- be-txt-0411 -->';
$cta = '<p>Le devis se fait toujours <strong>chez vous</strong> : un technicien vient gratuitement contrôler la faisabilité (conduit, emplacement, ventilation) et chiffrer précisément l\'installation. <a href="/rendez-vous/">Prendre rendez-vous avec un technicien</a> ou <a href="/devis/">préparer votre demande en ligne</a>.</p>';

$texts = [];

$texts['pages']['chauffage-mixte'] = <<<HTML
<h2>Le poêle mixte : le bois et les granulés dans un seul appareil</h2>
<p>Un poêle mixte, aussi appelé poêle hybride, fonctionne au choix avec des bûches ou avec des granulés de bois. Vous profitez de la vraie flambée quand vous êtes à la maison, et du confort automatique des granulés le reste du temps : allumage programmé, température régulée, autonomie de plusieurs heures grâce au réservoir.</p>
<p>C'est une solution appréciée en Bretagne, où les intersaisons sont longues : quelques granulés suffisent pour tempérer la maison le matin, et une flambée de bûches prend le relais le soir.</p>
<h2>Pourquoi choisir un poêle mixte ?</h2>
<h3>La souplesse du combustible</h3>
<p>Vous n'êtes jamais dépendant d'une seule énergie. Si vous avez accès à du bois, vous l'utilisez ; si vous êtes absent ou pressé, les granulés prennent le relais sans intervention.</p>
<h3>Le confort d'un chauffage programmable</h3>
<p>En mode granulés, le poêle s'allume et s'éteint selon vos horaires et maintient la température choisie. Selon les modèles, il se pilote avec un thermostat, une télécommande ou une application.</p>
<h3>Le plaisir de la flamme</h3>
<p>En mode bûches, vous retrouvez le crépitement et la chaleur rayonnante d'un poêle à bois traditionnel.</p>
<h2>Bien choisir son poêle mixte</h2>
<p>La puissance doit correspondre au volume à chauffer et à l'isolation de la maison : un appareil trop puissant fonctionne au ralenti et s'encrasse. Il faut aussi prévoir une prise électrique pour la partie granulés, un conduit adapté et un emplacement pour stocker les sacs. Toutes ces questions sont vérifiées lors de la visite technique.</p>
<h2>À voir dans nos showrooms</h2>
<p>Plusieurs poêles mixtes sont exposés dans nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes : venez comparer les modèles, les finitions et le fonctionnement des deux modes.</p>
$cta
HTML;

$texts['pages']['cuisson'] = <<<HTML
<h2>Cuisiner au feu de bois toute l'année</h2>
<p>Le feu ne sert pas qu'à chauffer : il cuisine aussi. Braises Émeraude propose des cuisinières à bois pour la maison, ainsi que des barbecues et des braseros pour profiter du feu dehors aux beaux jours.</p>
<h2>La cuisinière à bois : chauffer et cuisiner</h2>
<p>Une cuisinière à bois chauffe la pièce tout en offrant une plaque de cuisson et un four. Mijotés, pains, gratins : la chaleur douce et régulière du bois donne des cuissons incomparables. C'est aussi un véritable appareil de chauffage, qui peut tempérer une grande pièce de vie.</p>
<h3>Les cuisinières Rizzoli</h3>
<p>Nous présentons les cuisinières à bois Rizzoli, déclinées en plusieurs séries (L, M, N, R, S, ZVI) selon la taille du foyer, du four et le style recherché. <a href="/marque/rizzoli/">Découvrir la gamme Rizzoli</a>.</p>
<h3>Les poêles avec four</h3>
<p>Certains poêles à bois ou mixtes intègrent un compartiment de cuisson : une alternative compacte pour réchauffer un plat ou cuire au four sans cuisinière complète.</p>
<h2>Barbecues et braseros</h2>
<p>Pour l'extérieur, le brasero crée un point de convivialité autour du feu, et le barbecue au bois ou au charbon de bois donne aux grillades leur goût fumé. Demandez conseil en showroom pour choisir le modèle adapté à votre terrasse.</p>
<h2>Installation et conseils</h2>
<p>Une cuisinière à bois se raccorde à un conduit comme un poêle : son installation demande les mêmes vérifications (conduit, distances de sécurité, arrivée d'air). Nos équipes s'en chargent après une visite technique à domicile.</p>
$cta
HTML;

$texts['pages']['boutique'] = <<<HTML
<h2>Notre catalogue de poêles, inserts et cuisinières</h2>
<p>Retrouvez ici l'ensemble des appareils que nous proposons : poêles à bois, poêles à granulés, poêles mixtes, inserts et foyers pour cheminée, cuisinières à bois. Chaque fiche présente le modèle, ses caractéristiques et sa puissance.</p>
<h2>Comment utiliser le catalogue</h2>
<h3>Repérez vos modèles</h3>
<p>Parcourez les catégories <a href="/categorie-produit/chauffage-bois/">bois</a>, <a href="/categorie-produit/chauffage-granule/">granulés</a>, <a href="/categorie-produit/chauffage-mixte/">mixte</a> ou <a href="/categorie-produit/cuisson/">cuisson</a>, et ajoutez les appareils qui vous plaisent à votre projet.</p>
<h3>Venez les voir en vrai</h3>
<p>Une grande partie des modèles est exposée dans nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes : rien ne remplace la vision de la flamme et le toucher des finitions.</p>
<h3>Faites valider votre choix chez vous</h3>
<p>Le bon appareil dépend de votre maison : surface, isolation, conduit existant, emplacement. C'est pourquoi le choix définitif et le prix se font après la visite d'un technicien.</p>
<h2>Pas de vente en ligne : un conseil et une pose par des professionnels</h2>
<p>Un poêle ou un insert n'est pas un appareil que l'on branche simplement : son efficacité et sa sécurité dépendent de l'installation. Nous ne vendons donc pas en ligne ; nous accompagnons chaque projet du conseil à la pose, puis pour l'entretien.</p>
$cta
HTML;

$texts['pages']['politique-de-confidentialite'] = <<<HTML
<h2>Qui traite vos données ?</h2>
<p>Les données collectées sur ce site sont traitées par Braises Émeraude (La Soleillade), dont les coordonnées complètes figurent dans les <a href="/mentions-legales/">mentions légales</a>.</p>
<h2>Quelles données collectons-nous et pourquoi ?</h2>
<h3>Demandes de devis, de contact et de rendez-vous</h3>
<p>Lorsque vous remplissez un formulaire, nous recueillons les informations que vous saisissez : nom, téléphone, adresse e-mail, code postal, et selon le formulaire l'adresse du chantier, la description de votre projet et les photos que vous choisissez d'envoyer. Elles servent uniquement à répondre à votre demande, organiser la visite technique et établir votre devis. Elles sont transmises à l'équipe du magasin concerné et ne sont jamais vendues ni cédées à des tiers.</p>
<h3>Mesure d'audience sans cookie</h3>
<p>Pour connaître la fréquentation des pages, le site utilise une mesure d'audience interne, sans cookie et sans outil publicitaire. Aucune donnée n'est utilisée pour vous suivre sur d'autres sites.</p>
<h3>Cartes et vidéos</h3>
<p>Les cartes sont fournies par OpenStreetMap et ne se chargent qu'à l'affichage de la carte. Les vidéos hébergées sur YouTube ou Vimeo ne se chargent que si vous cliquez pour les lire ; ces services appliquent alors leur propre politique de confidentialité.</p>
<h2>Base légale et durée de conservation</h2>
<p>Le traitement repose sur votre consentement, recueilli au moment de l'envoi du formulaire, et sur les échanges préalables à un éventuel contrat. Les demandes sans suite sont conservées au maximum trois ans après le dernier contact ; les données liées à un chantier réalisé sont conservées pendant la durée nécessaire au suivi, à la garantie et aux obligations comptables.</p>
<h2>Vos droits</h2>
<p>Vous pouvez à tout moment accéder à vos données, les faire rectifier ou supprimer, vous opposer à leur traitement ou en demander la limitation, en écrivant à <a href="mailto:contact@braisesemeraude.com">contact@braisesemeraude.com</a>. Si vous estimez que vos droits ne sont pas respectés, vous pouvez adresser une réclamation à la CNIL (cnil.fr).</p>
<h2>Hébergement</h2>
<p>Le site est hébergé en France. Les données des formulaires sont stockées sur ce serveur et ne sont pas transférées hors de l'Union européenne par nos soins.</p>
HTML;

$texts['pages']['contact'] = <<<HTML
<h2>Comment nous joindre ?</h2>
<p>Pour toute question sur un poêle, un insert, l'entretien de votre appareil ou un projet en cours, l'équipe du magasin le plus proche de chez vous vous répond : Dinard · Pleurtuit pour la rive gauche de la Rance, Saint-Malo · Baguer-Morvan pour le pays malouin, Cancale et Dol, Rennes pour la métropole rennaise.</p>
<h2>Venir au showroom ou demander une visite technique ?</h2>
<h3>Le showroom : voir et comparer</h3>
<p>Nos showrooms vous permettent de voir les appareils en fonctionnement, de comparer les modèles et les finitions et d'avoir une idée des fourchettes de prix.</p>
<h3>La visite technique : obtenir votre devis</h3>
<p>Nous ne réalisons pas de devis en magasin. Chaque installation est différente : un technicien se déplace gratuitement chez vous pour vérifier le conduit, l'emplacement et la ventilation, puis chiffrer précisément votre projet.</p>
$cta
HTML;

$texts['pages']['chauffage-bois'] = <<<HTML
<h2>Poêle, insert ou foyer à bois : quelle solution pour votre maison ?</h2>
<h3>Le poêle à bois</h3>
<p>Il se pose presque partout pourvu qu'un conduit existe ou puisse être créé. Fonte, acier, pierre ollaire ou céramique : le matériau change la façon dont la chaleur est restituée, plus vive ou plus douce et durable.</p>
<h3>L'insert à bois</h3>
<p>Il se glisse dans une cheminée existante et la transforme en véritable chauffage, au lieu de laisser partir la chaleur par le conduit.</p>
<h3>Le foyer fermé</h3>
<p>Pour une cheminée créée sur mesure : vision panoramique, d'angle ou double face, le feu devient l'élément central de la pièce.</p>
<h2>Les atouts du chauffage au bois</h2>
<p>Le bois est une énergie locale et renouvelable. Un appareil moderne brûle mieux et consomme moins de bois qu'une cheminée ouverte, pour une chaleur rayonnante très agréable, particulièrement dans le climat breton plus humide que froid.</p>
<h2>Bien utiliser son appareil</h2>
<p>Brûlez un bois sec, stocké à l'abri, et faites ramoner le conduit comme la réglementation l'impose. Un appareil bien dimensionné et bien installé chauffe mieux et s'encrasse moins : c'est l'objet de la visite technique.</p>
$cta
HTML;

$texts['pages']['chauffage-granules'] = <<<HTML
<h2>Le chauffage aux granulés : le confort automatique</h2>
<p>Le poêle à granulés s'allume, se règle et s'éteint comme un chauffage classique. Son réservoir lui donne plusieurs heures d'autonomie, et la plupart des modèles se programment ou se pilotent à distance.</p>
<h2>Poêle ou insert à granulés ?</h2>
<h3>Le poêle à granulés</h3>
<p>Ventilé pour chauffer rapidement la pièce, canalisable pour envoyer de l'air chaud vers d'autres pièces, ou silencieux pour un séjour ou une chambre : il existe un modèle pour chaque usage.</p>
<h3>L'insert à granulés</h3>
<p>Il s'installe dans une cheminée existante pour la transformer en chauffage automatique, sans perdre la vision de la flamme.</p>
<h2>Ce qu'il faut prévoir</h2>
<p>Une prise électrique à proximité, un conduit adapté et un peu de place pour stocker les sacs de granulés. Le dimensionnement de l'appareil et son raccordement sont vérifiés lors de la visite technique à domicile.</p>
$cta
HTML;

$texts['pages']['nos-realisations'] = <<<HTML
<h2>Des installations réalisées par nos équipes</h2>
<p>Chaque chantier présenté ici a été réalisé par nos équipes, de la visite technique à la pose : poêles à bois, poêles à granulés, poêles mixtes, inserts et foyers, en Ille-et-Vilaine et sur la Côte d'Émeraude. Ces exemples montrent la diversité des intérieurs et des solutions possibles.</p>
<h2>Votre projet, étape par étape</h2>
<h3>1. Le choix au showroom</h3>
<p>Vous découvrez les modèles en fonctionnement et repérez ceux qui vous plaisent.</p>
<h3>2. La visite technique chez vous</h3>
<p>Un technicien vérifie le conduit, l'emplacement et la ventilation, puis établit un devis précis.</p>
<h3>3. La pose et la mise en service</h3>
<p>Nos équipes installent l'appareil, le mettent en service et vous expliquent son utilisation et son entretien.</p>
$cta
HTML;

$texts['pages']['showroom'] = <<<HTML
<h2>Que trouver dans nos showrooms ?</h2>
<p>Des poêles à bois, à granulés et mixtes, des inserts et des foyers, souvent en fonctionnement, pour voir la flamme, sentir la chaleur et comparer les finitions. Nos conseillers vous présentent les modèles et les fourchettes de prix.</p>
<h2>Et pour le devis ?</h2>
<p>Le showroom sert à choisir ; le devis, lui, se fait chez vous. Un technicien se déplace gratuitement pour contrôler la faisabilité de l'installation et la chiffrer précisément.</p>
$cta
HTML;

$texts['brands']['m-design'] = <<<HTML
<h2>M Design chez Braises Émeraude</h2>
<p>Braises Émeraude présente les foyers et poêles M Design, une marque connue pour ses appareils au design affirmé, pensés pour faire du feu l'élément central de la pièce.</p>
<h2>Des appareils à découvrir en showroom</h2>
<p>Un foyer ou un poêle design se choisit aussi sur sa présence dans la pièce : proportions, vision du feu, finitions. Venez découvrir les modèles et en parler avec nos conseillers dans nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes.</p>
<h2>Une installation sur mesure</h2>
<p>L'intégration d'un foyer demande une étude précise : dimensions, conduit, habillage et ventilation. Notre technicien vérifie tout cela chez vous avant de chiffrer le projet.</p>
$cta
HTML;

$words = fn (string $h) => str_word_count(strip_tags(html_entity_decode($h)), 0, 'àâäéèêëîïôöùûüçœÀÂÉÈÊÎÔÙÛÇ');
foreach ($texts as $table => $list) {
    foreach ($list as $slug => $html) {
        $row = DB::table($table)->where('slug', $slug)->first(['id', 'content']);
        if (! $row) {
            echo "ATTENTION introuvable : $table $slug\n";
            continue;
        }
        $current = (string) $row->content;
        if (str_contains($current, $M)) {
            echo str_pad("$table $slug", 45)." déjà fait\n";
            continue;
        }
        $mode = $words($current) < 60 ? 'remplacé' : 'complété';
        $new = $mode === 'remplacé' ? $M."\n".$html : rtrim($current)."\n".$M."\n".$html;
        echo str_pad("$table $slug", 45).' '.str_pad($mode, 9).' '.$words($current).' → '.$words($new)." mots\n";
        if ($apply) {
            DB::table($table)->where('id', $row->id)->update(['content' => $new, 'updated_at' => now()]);
        }
    }
}
echo $apply ? "APPLIQUÉ\n" : "SIMULATION (relancer avec --apply)\n";
$apply && cache()->flush();
