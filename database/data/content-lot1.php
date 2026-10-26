<?php

declare(strict_types=1);

/*
 * Contenus — lot 1 (26/10/2026) : 8 catégories, 11 marques, magasin de Dinard.
 * Injectés par ContentLot1Seeder : seuls les champs VIDES sont remplis, tout reste modifiable dans l'admin.
 * Règle : aucun fait non vérifié (pas de label, de prix ni d'origine de marque incertaine).
 */

return [

// ═══════════════════════════════════════════════ CATÉGORIES (clé = path)
'categories' => [

'chauffage-bois' => [
    'h1' => 'Poêles et inserts à bois',
    'seo_title' => 'Poêle et insert à bois en Ille-et-Vilaine',
    'seo_description' => 'Poêles à bois, inserts et foyers sélectionnés par Braises Émeraude : conseil, pose et entretien à Dinard, Saint-Malo et Rennes. Devis gratuit.',
    'intro' => 'La vraie flamme, le crépitement des bûches et une chaleur douce qui gagne toute la maison. Poêles, inserts et foyers à bois sélectionnés pour leur rendement et leur design, installés par nos équipes de Dinard, Saint-Malo et Rennes.',
    'content' => <<<'HTML'
<h2>Pourquoi choisir le chauffage au bois ?</h2>
<p>Le bois reste l'énergie de chauffage la plus naturelle qui soit. Il pousse près de chez nous, se stocke facilement et se renouvelle. Un appareil moderne n'a plus rien à voir avec la cheminée ouverte d'autrefois : la double combustion et l'arrivée d'air maîtrisée permettent de tirer bien plus de chaleur de chaque bûche, avec moins de fumée et moins de cendres.</p>
<p>En Bretagne, où l'hiver est plus humide que froid, un poêle à bois apporte aussi un vrai confort : la chaleur rayonnante assèche l'air, réchauffe les murs et les sols, et donne à la maison cette ambiance qu'aucun radiateur ne remplace.</p>

<h2>Poêle, insert ou foyer : quelle solution pour votre maison ?</h2>
<h3>Le poêle à bois</h3>
<p>Posé au sol ou suspendu, il se place presque partout pourvu qu'un conduit puisse être créé. C'est la solution la plus souple, idéale dans une pièce de vie ouverte ou une construction récente. Découvrez notre sélection de <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a>.</p>
<h3>L'insert à bois</h3>
<p>Il se glisse dans une cheminée existante et la transforme en véritable chauffage. Une cheminée ouverte laisse partir l'essentiel de la chaleur par le conduit ; un insert la récupère. Voir nos <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts à bois</a>.</p>
<h3>Le foyer fermé</h3>
<p>Pour une cheminée sur mesure, en création ou en rénovation complète : face vitrée, double face, vision d'angle ou panoramique, le foyer devient l'élément central de la pièce.</p>

<h2>Bien dimensionner son appareil</h2>
<p>Un poêle trop puissant fonctionne au ralenti, s'encrasse et chauffe mal ; un poêle trop faible tourne à plein régime en permanence. La bonne puissance dépend de la surface, du volume, de l'isolation et de la configuration de la maison. C'est pourquoi nous venons voir votre logement avant de vous conseiller : maisons en pierre, longères, villas de bord de mer ou constructions récentes n'ont pas les mêmes besoins.</p>

<h2>Le bois de chauffage : quelques conseils</h2>
<ul>
<li>Brûlez un bois sec, idéalement stocké deux ans à l'abri : un bois humide chauffe mal et encrasse le conduit.</li>
<li>Préférez les feuillus durs (chêne, hêtre, charme) pour une flambée longue.</li>
<li>Faites ramoner votre conduit chaque année : c'est une obligation et une question de sécurité.</li>
</ul>

<h2>Installation et entretien par nos équipes</h2>
<p>Chacun de nos trois showrooms expose des appareils en fonctionnement et dispose de sa propre équipe de pose et d'entretien. Nous nous occupons du conduit, de la pose et de la mise en service, puis de l'entretien saison après saison. Des aides peuvent réduire le coût de votre projet : consultez notre page <a href="/aides-financement/">aides et financement</a> ou <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'chauffage-bois/poele-chauffage-bois' => [
    'h1' => 'Poêles à bois',
    'seo_title' => 'Poêle à bois à Saint-Malo, Dinard et Rennes',
    'seo_description' => 'Poêles à bois en fonte, acier ou à accumulation : choix, pose et entretien par Braises Émeraude dans nos showrooms de Dinard, Saint-Malo et Rennes.',
    'intro' => 'Fonte, acier, pierre ollaire ou céramique, posés au sol ou suspendus : nos poêles à bois sont choisis pour leur rendement, leur qualité de fabrication et leur ligne. Venez les voir en fonctionnement dans nos showrooms.',
    'content' => <<<'HTML'
<h2>Un poêle à bois pour chaque intérieur</h2>
<p>Le poêle à bois est la façon la plus simple de profiter d'un vrai feu. Il ne demande pas de cheminée existante : un conduit adapté suffit. Contemporain ou classique, compact ou imposant, il trouve sa place dans une longère comme dans une maison neuve.</p>

<h3>Les poêles en fonte</h3>
<p>La fonte monte en température plus lentement mais restitue la chaleur longtemps après l'extinction du feu. C'est un matériau robuste, apprécié pour sa durée de vie et son rayonnement doux.</p>
<h3>Les poêles en acier</h3>
<p>Plus réactifs, ils chauffent rapidement la pièce et autorisent des lignes très épurées : grandes vitres, formes cylindriques, modèles suspendus.</p>
<h3>Les poêles à accumulation</h3>
<p>Habillés de pierre ou de céramique, ils emmagasinent la chaleur pendant la flambée et la restituent pendant de longues heures. Une ou deux flambées par jour suffisent souvent : c'est la solution idéale pour qui veut chauffer longtemps en rechargeant peu.</p>

<h2>Les critères pour bien choisir</h2>
<ul>
<li><strong>La puissance</strong> : elle se calcule selon la surface, l'isolation et le volume à chauffer.</li>
<li><strong>Le rendement</strong> : plus il est élevé, moins vous consommez de bois pour la même chaleur.</li>
<li><strong>L'autonomie</strong> : la taille du foyer et la longueur des bûches acceptées comptent au quotidien.</li>
<li><strong>Le raccordement</strong> : sortie de fumée par le dessus ou l'arrière, arrivée d'air extérieur pour les maisons bien isolées.</li>
<li><strong>Le design</strong> : vision du feu, finition, couleur, socle ou pieds.</li>
</ul>

<h2>Voir le feu avant de choisir</h2>
<p>Dans nos showrooms de <a href="/poele-a-bois-granules-dinard/">Dinard</a>, <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo</a> et <a href="/poele-a-bois-granules-rennes/">Rennes</a>, plusieurs poêles fonctionnent pour que vous puissiez juger de la flamme, de la chaleur et de la finition. Nos conseillers étudient ensuite votre maison, le conduit existant ou à créer, et vous remettent un devis détaillé, pose comprise.</p>
<p>Vous hésitez encore entre bois et granulés ? Comparez avec nos <a href="/categorie-produit/chauffage-granule/poele/">poêles à granulés</a> ou nos <a href="/categorie-produit/chauffage-mixte/">poêles mixtes</a>, qui acceptent les deux combustibles.</p>
HTML,
],

'chauffage-bois/insert-chauffage-bois' => [
    'h1' => 'Inserts et foyers à bois',
    'seo_title' => 'Insert et foyer à bois : transformer sa cheminée',
    'seo_description' => 'Transformez votre cheminée ouverte en vrai chauffage avec un insert ou un foyer à bois. Conseil, pose et entretien par Braises Émeraude.',
    'intro' => 'Votre cheminée ouverte chauffe surtout le conduit ? Un insert ou un foyer fermé la transforme en véritable chauffage, sans renoncer au spectacle du feu. Nos équipes s\'occupent de tout, de l\'étude à la mise en service.',
    'content' => <<<'HTML'
<h2>Insert ou foyer : quelle différence ?</h2>
<p><strong>L'insert</strong> vient s'encastrer dans une cheminée existante. C'est la solution de rénovation par excellence : on conserve l'habillage, on ferme le foyer et on gagne immédiatement en chaleur et en sécurité.</p>
<p><strong>Le foyer fermé</strong> est conçu pour être habillé sur mesure. Il permet de créer une nouvelle cheminée ou de refaire entièrement l'ancienne : face vitrée, double face visible depuis deux pièces, vision d'angle ou panoramique.</p>

<h2>Les avantages d'un foyer fermé</h2>
<ul>
<li><strong>Bien plus de chaleur</strong> : une cheminée ouverte laisse partir l'essentiel de son énergie par le conduit, un appareil fermé la récupère.</li>
<li><strong>Moins de bois</strong> : la combustion est maîtrisée, les bûches durent plus longtemps.</li>
<li><strong>Plus de sécurité</strong> : plus d'escarbilles sur le parquet ni de courants d'air froid quand le feu est éteint.</li>
<li><strong>Une vitre propre</strong> : le balayage d'air limite le noircissement.</li>
</ul>

<h2>Une étude avant la pose</h2>
<p>Chaque cheminée est différente : dimensions de l'âtre, état et diamètre du conduit, matériaux de l'habillage. Avant de proposer un appareil, nos techniciens viennent mesurer et vérifier le conduit. Si nécessaire, nous le tubons pour garantir un bon tirage et la conformité de l'installation.</p>

<h2>Distribuer la chaleur dans la maison</h2>
<p>Certains foyers peuvent être raccordés à des gaines pour envoyer l'air chaud vers une pièce voisine ou à l'étage. Une bonne façon de profiter du feu au-delà du salon : parlons-en lors de la visite.</p>

<h2>Questions fréquentes</h2>
<h3>Peut-on installer un insert dans n'importe quelle cheminée ?</h3>
<p>Dans la plupart des cas, oui, à condition que l'âtre ait des dimensions suffisantes et que le conduit soit en bon état ou puisse être tubé. La visite technique permet de le vérifier avant tout engagement.</p>
<h3>Combien de temps dure la pose ?</h3>
<p>Une pose d'insert se fait généralement en une journée, tubage compris, selon la configuration de la cheminée.</p>
<h3>Faut-il refaire l'habillage de la cheminée ?</h3>
<p>Pas forcément : un cadre de finition permet souvent de conserver l'habillage existant autour de l'insert.</p>

<p>Préférez-vous la simplicité des granulés ? Nous installons aussi des <a href="/categorie-produit/chauffage-granule/insert/">inserts à granulés</a>, programmables et autonomes. Pour comparer, venez en showroom ou <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'chauffage-granule' => [
    'h1' => 'Poêles et inserts à granulés',
    'seo_title' => 'Poêle à granulés en Ille-et-Vilaine',
    'seo_description' => 'Poêles et inserts à granulés programmables, pilotables à distance : choix, pose et entretien par Braises Émeraude à Dinard, Saint-Malo et Rennes.',
    'intro' => 'Programmable, autonome et pilotable depuis votre téléphone, le poêle à granulés offre le confort d\'un chauffage automatique avec la chaleur d\'une vraie flamme. Une solution idéale en chauffage principal.',
    'content' => <<<'HTML'
<h2>Le confort d'un chauffage automatique</h2>
<p>Le granulé de bois, aussi appelé pellet, est fabriqué à partir de sciure compressée. Il est régulier, sec et facile à stocker en sacs. Le poêle s'en alimente seul grâce à un réservoir et une vis sans fin : vous réglez la température souhaitée, l'appareil fait le reste.</p>
<ul>
<li><strong>Programmation</strong> : allumage le matin avant le réveil, baisse la nuit, mode absence.</li>
<li><strong>Pilotage à distance</strong> : la plupart des modèles se commandent depuis une application, pratique pour une résidence secondaire.</li>
<li><strong>Autonomie</strong> : selon le réservoir et l'allure de chauffe, de un à plusieurs jours sans recharge.</li>
<li><strong>Régulation fine</strong> : la puissance s'adapte à la température de la pièce.</li>
</ul>

<h2>Poêle ou insert à granulés ?</h2>
<p>Le <a href="/categorie-produit/chauffage-granule/poele/">poêle à granulés</a> s'installe librement dans la pièce de vie, avec une ventouse ou un conduit. L'<a href="/categorie-produit/chauffage-granule/insert/">insert à granulés</a> prend place dans une cheminée existante pour la transformer en chauffage automatique.</p>

<h2>Ventilé ou silencieux ?</h2>
<p>Les modèles ventilés diffusent rapidement la chaleur dans la pièce. Les modèles à convection naturelle, sans ventilateur, sont plus silencieux : appréciables dans une chambre ou un séjour calme. Certains appareils peuvent aussi être canalisés pour chauffer une seconde pièce.</p>

<h2>Bien choisir ses granulés</h2>
<p>La qualité du granulé conditionne le rendement et l'entretien de l'appareil. Choisissez des granulés certifiés, peu cendreux, et stockez-les au sec. Nos conseillers vous orientent vers des granulés adaptés à votre poêle.</p>

<h2>Entretien</h2>
<p>Un poêle à granulés demande un petit nettoyage régulier (brasero, bac à cendres) et un entretien annuel par un professionnel, en plus du ramonage du conduit. Nos équipes assurent ce suivi dans nos trois secteurs.</p>

<p>Des aides peuvent alléger l'investissement : consultez notre page <a href="/aides-financement/">aides et financement</a>, puis <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'chauffage-granule/poele' => [
    'h1' => 'Poêles à granulés',
    'seo_title' => 'Poêle à granulés programmable : notre sélection',
    'seo_description' => 'Poêles à granulés ventilés, silencieux ou canalisables, pilotables depuis votre téléphone. Pose et entretien par Braises Émeraude.',
    'intro' => 'Ventilés, silencieux ou canalisables, compacts pour un petit séjour ou puissants pour toute la maison : nos poêles à granulés allient confort automatique et belle flamme.',
    'content' => <<<'HTML'
<h2>Un chauffage qui s'adapte à votre rythme</h2>
<p>Le poêle à granulés démarre seul à l'heure programmée, maintient la température choisie et se met en veille quand la pièce est chaude. Vous rechargez le réservoir, il s'occupe du reste. Pour une maison principale comme pour une résidence secondaire, c'est le confort d'un chauffage central avec la présence d'une flamme.</p>

<h2>Les familles de poêles à granulés</h2>
<h3>Poêles ventilés</h3>
<p>Un ventilateur pulse l'air chaud dans la pièce : la montée en température est rapide, la chaleur bien répartie.</p>
<h3>Poêles silencieux</h3>
<p>Sans ventilation forcée, ils chauffent par convection naturelle et rayonnement. Idéals pour les pièces où le calme compte.</p>
<h3>Poêles canalisables</h3>
<p>Une partie de l'air chaud est envoyée par gaines vers une ou deux autres pièces, comme une chambre ou un bureau.</p>
<h3>Poêles étroits ou d'angle</h3>
<p>Pour les couloirs, les entrées ou les petits espaces, certains modèles ne font que quelques dizaines de centimètres de profondeur.</p>

<h2>Ce qu'il faut regarder avant d'acheter</h2>
<ul>
<li>La plage de puissance, à adapter à la surface et à l'isolation.</li>
<li>La capacité du réservoir et donc l'autonomie.</li>
<li>Le niveau sonore, si le poêle est dans une pièce calme.</li>
<li>La connectivité : application, thermostat d'ambiance, programmation hebdomadaire.</li>
<li>Le type d'évacuation possible chez vous : conduit existant ou ventouse.</li>
</ul>

<h2>Questions fréquentes</h2>
<h3>Un poêle à granulés peut-il chauffer toute la maison ?</h3>
<p>Dans une maison ouverte et bien isolée, oui. Dans une maison cloisonnée, un modèle canalisable aide à répartir la chaleur vers les autres pièces.</p>
<h3>Faut-il un conduit de cheminée ?</h3>
<p>Pas toujours : beaucoup de modèles peuvent être raccordés en ventouse, selon la configuration du logement et la réglementation.</p>
<h3>Que se passe-t-il en cas de coupure de courant ?</h3>
<p>Le poêle à granulés a besoin d'électricité pour fonctionner. Il s'arrête en sécurité et redémarre au retour du courant.</p>

<p>Nos conseillers vous aident à comparer les modèles exposés à <a href="/poele-a-bois-granules-dinard/">Dinard</a>, <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo</a> et <a href="/poele-a-bois-granules-rennes/">Rennes</a>. Ajoutez vos coups de cœur à « Mon projet » puis <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'chauffage-granule/insert' => [
    'h1' => 'Inserts à granulés',
    'seo_title' => 'Insert à granulés : chauffer avec sa cheminée',
    'seo_description' => 'L\'insert à granulés transforme votre cheminée en chauffage automatique et programmable. Étude, pose et entretien par Braises Émeraude.',
    'intro' => 'Gardez votre cheminée, gagnez le confort d\'un chauffage automatique : l\'insert à granulés s\'installe dans l\'âtre existant et se programme comme un thermostat.',
    'content' => <<<'HTML'
<h2>La cheminée devient un chauffage programmable</h2>
<p>Une cheminée ouverte est agréable mais chauffe peu et demande de la présence. L'insert à granulés change tout : il s'allume à l'heure voulue, régule la température et fonctionne plusieurs heures sans intervention. Vous conservez l'habillage de votre cheminée et le plaisir de voir la flamme.</p>

<h2>Comment se passe l'installation ?</h2>
<ol>
<li><strong>Visite technique</strong> : mesure de l'âtre, contrôle du conduit, repérage de l'alimentation électrique.</li>
<li><strong>Choix de l'appareil</strong> : dimensions, puissance, ventilé ou non, possibilité de canaliser l'air chaud.</li>
<li><strong>Tubage du conduit</strong> si nécessaire, pour une évacuation conforme.</li>
<li><strong>Pose et mise en service</strong>, avec la prise en main de la programmation.</li>
</ol>

<h2>Les points forts de l'insert à granulés</h2>
<ul>
<li>Allumage et extinction automatiques.</li>
<li>Pilotage à distance sur de nombreux modèles.</li>
<li>Réservoir intégré, souvent accessible par le dessus ou par l'avant.</li>
<li>Rendement bien supérieur à celui d'une cheminée ouverte.</li>
</ul>

<h2>Questions fréquentes</h2>
<h3>L'insert à granulés a-t-il besoin d'électricité ?</h3>
<p>Oui, pour l'allumage, la vis d'alimentation et la ventilation. Une prise doit donc être prévue à proximité de la cheminée.</p>
<h3>Comment recharger les granulés ?</h3>
<p>Selon les modèles, le réservoir se remplit par le dessus ou par une trappe en façade. Nous en tenons compte dans le choix de l'appareil.</p>
<h3>Est-il bruyant ?</h3>
<p>Les modèles ventilés produisent un léger souffle ; certains modes de fonctionnement plus doux réduisent le bruit en soirée.</p>

<h2>Bois ou granulés dans votre cheminée ?</h2>
<p>L'<a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">insert à bois</a> séduit par sa flamme et son autonomie électrique ; l'insert à granulés par sa programmation. Nos conseillers vous présentent les deux en showroom pour vous aider à décider. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'chauffage-mixte' => [
    'h1' => 'Poêles mixtes bois et granulés',
    'seo_title' => 'Poêle mixte bois et granulés',
    'seo_description' => 'Poêles mixtes acceptant bûches et granulés : la flamme du bois, la programmation des pellets. Conseil et pose par Braises Émeraude.',
    'intro' => 'Pourquoi choisir ? Le poêle mixte accepte les bûches et les granulés : la flamme du bois quand vous êtes là, l\'automatisme des pellets quand vous êtes absent.',
    'content' => <<<'HTML'
<h2>Le meilleur des deux combustibles</h2>
<p>Le poêle mixte, parfois appelé hybride, réunit dans un même appareil un foyer à bûches et un système à granulés. Le soir, vous profitez du crépitement d'un vrai feu de bois. Le matin ou pendant vos absences, les granulés prennent le relais automatiquement pour maintenir la maison au chaud.</p>

<h2>À qui s'adresse le poêle mixte ?</h2>
<ul>
<li>Aux foyers qui disposent de bois mais veulent un chauffage qui fonctionne aussi en leur absence.</li>
<li>Aux résidences secondaires, que l'on veut trouver chaudes en arrivant.</li>
<li>À ceux qui hésitent entre bois et granulés et ne veulent pas renoncer à l'un ou à l'autre.</li>
</ul>

<h2>Comment ça fonctionne ?</h2>
<p>Selon les modèles, le passage d'un combustible à l'autre se fait manuellement ou automatiquement : quand la dernière bûche est consumée, l'appareil peut démarrer sur les granulés pour garder la température demandée. La programmation et, souvent, le pilotage à distance sont ceux d'un poêle à granulés.</p>

<h2>Les points à vérifier</h2>
<ul>
<li>La puissance en mode bois et en mode granulés, qui peut différer.</li>
<li>La capacité du réservoir à granulés.</li>
<li>Le mode de bascule entre les deux combustibles.</li>
<li>L'entretien, un peu plus complet que pour un appareil simple.</li>
</ul>

<h2>Questions fréquentes</h2>
<h3>Un poêle mixte chauffe-t-il aussi bien qu'un poêle simple ?</h3>
<p>Oui, chaque mode est conçu pour fonctionner pleinement. Il faut simplement vérifier la puissance annoncée pour le bois et pour les granulés.</p>
<h3>Peut-on n'utiliser que des granulés ?</h3>
<p>Oui : vous pouvez n'utiliser que les granulés une partie de l'hiver et réserver les bûches aux soirées.</p>
<h3>L'entretien est-il plus contraignant ?</h3>
<p>Un peu plus complet, puisqu'il concerne les deux systèmes, mais nos équipes s'en chargent lors de la visite annuelle.</p>

<p>Ces appareils demandent un vrai conseil : venez les voir dans nos showrooms de <a href="/poele-a-bois-granules-dinard/">Dinard</a>, <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo</a> ou <a href="/poele-a-bois-granules-rennes/">Rennes</a>, ou comparez avec nos <a href="/categorie-produit/chauffage-bois/">poêles à bois</a> et <a href="/categorie-produit/chauffage-granule/">poêles à granulés</a>. <a href="/devis/">Devis gratuit</a>.</p>
HTML,
],

'cuisson' => [
    'h1' => 'Cuisinières à bois, barbecues et braseros',
    'seo_title' => 'Cuisinière à bois, barbecue et brasero',
    'seo_description' => 'Cuisinières à bois pour chauffer et cuisiner l\'hiver, barbecues et braseros l\'été : le feu toute l\'année avec Braises Émeraude.',
    'intro' => 'Le feu ne sert pas qu\'à chauffer. Cuisinières à bois l\'hiver, barbecues et braseros l\'été : chez Braises Émeraude, la flamme se partage toute l\'année.',
    'content' => <<<'HTML'
<h2>La cuisinière à bois : chauffer et cuisiner</h2>
<p>La cuisinière à bois fait son retour dans les cuisines. Elle chauffe la pièce, parfois toute la maison, tout en offrant une plaque de cuisson et un four. Mijotés, pain, gratins : la chaleur douce et régulière du bois donne aux plats un goût inimitable.</p>
<h3>Ce qu'il faut regarder</h3>
<ul>
<li>La taille du four et de la plaque, selon votre façon de cuisiner.</li>
<li>La puissance de chauffe, à adapter à la pièce ou à la maison.</li>
<li>La finition : émail, acier, inox, couleur, pour s'accorder à la cuisine.</li>
<li>Le sens d'ouverture et l'emplacement du foyer, à gauche ou à droite.</li>
</ul>

<h2>Les poêles avec four</h2>
<p>Certains poêles à bois intègrent un four ou une plaque de cuisson. Une belle solution pour la pièce de vie : on chauffe, on regarde le feu et on cuisine en même temps.</p>

<h2>Barbecues et braseros pour l'été</h2>
<p>Quand vient la belle saison, nos magasins proposent aussi des barbecues et des braseros. Cuisson au feu de bois, plancha, soirées autour du brasero au jardin ou en terrasse : le plaisir du feu se prolonge dehors.</p>

<h2>Questions fréquentes</h2>
<h3>Une cuisinière à bois peut-elle chauffer toute la maison ?</h3>
<p>Selon sa puissance et la configuration de la maison, elle peut chauffer bien au-delà de la cuisine, surtout dans un plan ouvert.</p>
<h3>Le four chauffe-t-il vite ?</h3>
<p>Le four monte en température avec le feu ; on apprend vite à régler l'allure pour cuire un gratin, un pain ou un mijoté.</p>

<h2>Conseil et installation</h2>
<p>Une cuisinière à bois se raccorde à un conduit comme un poêle. Nos équipes étudient votre cuisine, le conduit et la ventilation, puis assurent la pose et l'entretien.</p>

<p>Venez découvrir notre sélection dans nos showrooms de <a href="/poele-a-bois-granules-dinard/">Dinard</a>, <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo</a> et <a href="/poele-a-bois-granules-rennes/">Rennes</a>, ou <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

],

// ═══════════════════════════════════════════════ MARQUES (clé = slug)
'brands' => [

'austroflamm' => [
    'country' => 'Autriche',
    'h1' => 'Poêles Austroflamm',
    'seo_title' => 'Poêles Austroflamm bois, granulés et mixtes',
    'seo_description' => 'Poêles Austroflamm à bois, à granulés, mixtes et avec four : découvrez la gamme chez Braises Émeraude à Dinard, Saint-Malo et Rennes.',
    'intro' => 'Fabricant autrichien reconnu pour la qualité de ses poêles, Austroflamm propose des modèles à bois, à granulés, mixtes et avec four, aux lignes contemporaines.',
    'content' => <<<'HTML'
<h2>Austroflamm, la précision autrichienne</h2>
<p>Austroflamm conçoit des poêles au design soigné et à la finition exigeante. La marque se distingue par la diversité de ses technologies : on trouve dans sa gamme des poêles à bois, des poêles à granulés, des poêles mixtes qui acceptent les deux combustibles, et des modèles équipés d'un four.</p>

<h2>Les gammes présentées chez Braises Émeraude</h2>
<h3>Poêles à bois</h3>
<p>La famille Clou, avec ses versions Xtra, Compact et Back, ainsi que les modèles Heidi et Vienne : des poêles à bois aux lignes nettes, adaptés aussi bien aux grandes pièces de vie qu'aux espaces plus compacts.</p>
<h3>Poêles mixtes « Duo »</h3>
<p>Les modèles Clou Duo, Mo Duo et Scotty Duo acceptent bûches et granulés. Le feu de bois quand vous êtes là, la programmation des granulés quand vous êtes absent. Voir nos <a href="/categorie-produit/chauffage-mixte/">poêles mixtes</a>.</p>
<h3>Poêles à granulés</h3>
<p>Clou Pellet et Clou Compact Pellets reprennent le design de la famille Clou avec le confort d'un chauffage automatique.</p>
<h3>Poêles avec four</h3>
<p>Les versions Cook, comme le Clou Compact Cook ou le Mo Duo Cook, ajoutent un four pour cuisiner au feu de bois. À découvrir aussi dans notre rubrique <a href="/categorie-produit/cuisson/">cuisson</a>.</p>

<h2>Voir un Austroflamm en fonctionnement</h2>
<p>Rien ne vaut une flamme réelle pour se faire une idée. Contactez votre showroom pour savoir quels modèles sont exposés, puis <a href="/devis/">demandez votre devis gratuit</a> : nos équipes s'occupent de l'étude, de la pose et de l'entretien.</p>
HTML,
],

'romotop' => [
    'country' => 'République tchèque',
    'h1' => 'Poêles et foyers Romotop',
    'seo_title' => 'Poêles et foyers Romotop',
    'seo_description' => 'Poêles à bois, poêles à accumulation et foyers Romotop : large choix de designs, conseil et pose par Braises Émeraude.',
    'intro' => 'Fabricant tchèque de poêles et de foyers à bois, Romotop propose l\'une des gammes les plus larges du marché : poêles en acier, poêles à accumulation et foyers pour cheminées sur mesure.',
    'content' => <<<'HTML'
<h2>Romotop, un très large choix de poêles à bois</h2>
<p>Romotop fabrique des poêles et des foyers à bois depuis le début des années 1990. La marque se distingue par l'étendue de son catalogue : formes rondes ou rectangulaires, habillages acier, pierre ou céramique, vision du feu frontale, latérale ou panoramique. Il y a presque toujours un Romotop adapté à la pièce et au style recherché.</p>

<h2>Les modèles proposés chez Braises Émeraude</h2>
<h3>Poêles à bois</h3>
<p>Evora, Burgos, Alpera, Alea, Riano, Stromboli, Ovalis, Laredo ou Navia : des poêles aux personnalités très différentes, du plus compact au plus imposant. Retrouvez-les dans nos <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a>.</p>
<h3>Poêles à accumulation</h3>
<p>Plusieurs modèles existent en version accumulation (Evora, Alea, Laredo, Ovalis). Leur masse emmagasine la chaleur pendant la flambée et la restitue pendant de longues heures : moins de rechargements pour une chaleur plus régulière.</p>
<h3>Foyers</h3>
<p>Les foyers Romotop (2G, 2GL, C2GL) se destinent aux cheminées créées ou rénovées sur mesure, avec des vitrages simples, d'angle ou multiples. Voir nos <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts et foyers à bois</a>.</p>

<h2>Bien choisir son Romotop</h2>
<p>Avec un catalogue aussi riche, le conseil est essentiel : puissance, type de vision, accumulation ou non, sortie de fumée. Nos conseillers étudient votre maison et vous orientent vers le bon modèle. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'dovre' => [
    'country' => 'Belgique',
    'h1' => 'Poêles et inserts Dovre',
    'seo_title' => 'Poêles et inserts en fonte Dovre',
    'seo_description' => 'Poêles et inserts à bois Dovre, spécialiste belge de la fonte : conseil, pose et entretien par Braises Émeraude.',
    'intro' => 'Marque belge historique, Dovre est réputée pour ses poêles et inserts à bois en fonte, robustes et faits pour durer.',
    'content' => <<<'HTML'
<h2>Dovre, le savoir-faire de la fonte</h2>
<p>Dovre est l'une des marques de référence du poêle en fonte. Ce matériau monte doucement en température puis restitue sa chaleur longtemps après la dernière flambée. Les appareils Dovre sont connus pour leur solidité et leur longévité, avec des lignes qui vont du classique au contemporain.</p>

<h2>La gamme chez Braises Émeraude</h2>
<h3>Poêles à bois</h3>
<p>Les séries 640 WD et 760 WD, ainsi que les modèles 2020S, 2120S et 2620SCB : des poêles en fonte de différentes tailles, pour une petite pièce comme pour un grand séjour. Retrouvez-les parmi nos <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a>.</p>
<h3>Insert</h3>
<p>L'insert 2520 SB s'installe dans une cheminée existante pour la transformer en véritable chauffage. Voir nos <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts à bois</a>.</p>

<h2>Pourquoi choisir la fonte ?</h2>
<ul>
<li>Une chaleur douce et durable, qui persiste après l'extinction.</li>
<li>Un matériau très résistant aux hautes températures.</li>
<li>Un style intemporel qui s'accorde aux maisons anciennes comme aux intérieurs modernes.</li>
</ul>

<p>Nos équipes assurent l'étude, la pose et l'entretien de votre poêle Dovre. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'brunner' => [
    'country' => 'Allemagne',
    'h1' => 'Foyers et poêles Brunner',
    'seo_title' => 'Foyers Brunner : cheminées sur mesure',
    'seo_description' => 'Foyers Brunner double face, d\'angle ou panoramiques et poêles Iron Dog : la cheminée haut de gamme avec Braises Émeraude.',
    'intro' => 'Fabricant allemand haut de gamme, Brunner est spécialisé dans les foyers pour cheminées sur mesure et propose aussi les poêles Iron Dog.',
    'content' => <<<'HTML'
<h2>Brunner, la cheminée sur mesure</h2>
<p>Brunner s'est fait un nom auprès des architectes et des amateurs de belles cheminées. Ses foyers sont conçus pour être intégrés dans un habillage créé sur mesure, en maçonnerie ou en matériaux contemporains, et offrent de multiples configurations de vitrage.</p>

<h2>Les foyers Brunner</h2>
<ul>
<li><strong>Foyer double face</strong> : le feu se voit depuis deux pièces, idéal entre un salon et une salle à manger.</li>
<li><strong>Foyer panoramique</strong> : un vitrage sur trois côtés pour une vision maximale de la flamme.</li>
<li><strong>Foyer d'angle</strong> : une vision sur deux côtés qui met en valeur l'angle de la pièce.</li>
</ul>
<p>Ces foyers sont présentés dans notre rubrique <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts et foyers à bois</a>.</p>

<h2>Les poêles Iron Dog</h2>
<p>Avec les Iron Dog N°4 et N°6, Brunner propose des poêles à bois au design brut et graphique, en acier épais. Voir nos <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a>.</p>

<h2>Un projet qui se prépare</h2>
<p>Une cheminée sur mesure demande une étude précise : structure, conduit, habillage, arrivée d'air. Nos équipes vous accompagnent de la conception à la mise en service. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'turbo-fonte' => [
    'h1' => 'Poêles Turbo Fonte',
    'seo_title' => 'Poêles à bois Turbo Fonte',
    'seo_description' => 'Poêles à bois Turbo Fonte sur socle, muraux ou suspendus : Jazz, Orion, Callisto, Tivoli. Conseil et pose par Braises Émeraude.',
    'intro' => 'Poêles sur socle, muraux ou compacts : les modèles Turbo Fonte jouent la carte du design pour faire du feu un élément de décoration.',
    'content' => <<<'HTML'
<h2>Turbo Fonte, le poêle comme objet de décoration</h2>
<p>Les poêles Turbo Fonte se reconnaissent à leurs lignes affirmées. Certains se fixent au mur et semblent flotter, d'autres reposent sur un socle ; tous mettent la flamme en scène.</p>

<h2>Les modèles présentés chez Braises Émeraude</h2>
<h3>La famille Jazz</h3>
<p>Jazz mural, Jazz socle et Jazz XL déclinent un même design en plusieurs versions : fixé au mur pour libérer le sol, posé sur socle, ou en grand format pour les pièces plus vastes.</p>
<h3>Les autres modèles</h3>
<p>Orion 2, Callisto 2, Tivoli CV, Mably mural et Karl 100 DF complètent la gamme, avec des formes rondes, rectangulaires ou double face selon les modèles.</p>

<h2>Bien installer un poêle mural ou suspendu</h2>
<p>Un poêle fixé au mur demande une paroi capable de supporter son poids et un raccordement soigné au conduit. Nos techniciens vérifient la faisabilité lors de la visite et réalisent une pose dans les règles.</p>

<p>Découvrez toute notre sélection de <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a> et <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'lorflam' => [
    'h1' => 'Inserts et foyers Lorflam',
    'seo_title' => 'Inserts et foyers à bois Lorflam',
    'seo_description' => 'Inserts et foyers à bois Lorflam (Ofen, XP Box, XP78) pour rénover ou créer une cheminée. Étude et pose par Braises Émeraude.',
    'intro' => 'Avec Lorflam, Braises Émeraude propose des inserts et des foyers à bois pour rénover une cheminée existante ou en créer une nouvelle.',
    'content' => <<<'HTML'
<h2>Lorflam, la cheminée réinventée</h2>
<p>Les appareils Lorflam que nous proposons sont des modèles à encastrer : inserts pour les cheminées existantes et foyers pour les habillages sur mesure. De quoi transformer une cheminée ouverte peu efficace en véritable chauffage, ou créer une cheminée contemporaine.</p>

<h2>Les gammes chez Braises Émeraude</h2>
<h3>La gamme Ofen</h3>
<p>Les modèles Ofen SP1, SP2, SP3 et SP4 offrent plusieurs largeurs et configurations pour s'adapter aux dimensions de votre cheminée.</p>
<h3>XP Box et XP78</h3>
<p>Des foyers aux lignes épurées, dont la version XP78 à cadre, pour une grande vision du feu dans un habillage sur mesure.</p>

<h2>L'importance de l'étude technique</h2>
<p>Un insert doit être choisi au centimètre près : largeur et profondeur de l'âtre, hauteur, diamètre et état du conduit. Nos techniciens viennent mesurer avant de proposer un modèle et tubent le conduit si nécessaire.</p>

<p>Retrouvez nos <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts et foyers à bois</a> et <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

'mcz' => [
    'country' => 'Italie',
    'h1' => 'Poêles à granulés MCZ',
    'seo_title' => 'Poêles à granulés MCZ',
    'seo_description' => 'Poêles et inserts à granulés MCZ, connectés et programmables : large choix de modèles, pose et entretien par Braises Émeraude.',
    'intro' => 'Fabricant italien de référence du chauffage au granulé, MCZ propose une très large gamme de poêles connectés, ventilés ou silencieux, et d\'inserts à granulés.',
    'content' => <<<'HTML'
<h2>MCZ, le spécialiste italien du granulé</h2>
<p>MCZ est l'une des marques les plus présentes sur le marché du poêle à granulés. Ses appareils associent design italien et technologie : programmation, pilotage depuis une application, ventilation réglable et, sur de nombreux modèles, fonctionnement silencieux.</p>

<h2>Les technologies Comfort Air et Air</h2>
<p>Une grande partie de la gamme MCZ repose sur la technologie à air : la chaleur est diffusée par ventilation pour chauffer rapidement la pièce. Les versions « Comfort Air » permettent en plus de canaliser l'air chaud vers une ou plusieurs autres pièces.</p>

<h2>Les modèles présentés chez Braises Émeraude</h2>
<p>Notre sélection couvre des styles très différents : formes arrondies comme Curve, Halo ou Cute, lignes droites comme Suite, Ego ou Tecla, modèles décoratifs comme Deco ou Tilda, grands formats comme Club, Musa, Star ou Stream, et le Vivo 90 pour une grande vision de flamme. Pour les cheminées existantes, MCZ propose aussi des inserts à granulés comme la gamme BoxTherm.</p>
<p>Retrouvez-les dans nos <a href="/categorie-produit/chauffage-granule/poele/">poêles à granulés</a> et <a href="/categorie-produit/chauffage-granule/insert/">inserts à granulés</a>.</p>

<h2>Bien choisir son MCZ</h2>
<ul>
<li>La puissance, selon la surface et l'isolation.</li>
<li>Ventilé, canalisable ou silencieux, selon la pièce.</li>
<li>La place disponible : certains modèles sont très étroits.</li>
</ul>

<p>Nos équipes assurent la pose, la mise en service et l'entretien annuel de votre poêle MCZ. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'stoveitaly' => [
    'h1' => 'Poêles et inserts Stoveitaly',
    'seo_title' => 'Poêles et inserts à granulés Stoveitaly',
    'seo_description' => 'Poêles et inserts à granulés Stoveitaly (Piano, Gaio, Alaska, Compact, Duetto) : conseil et pose par Braises Émeraude.',
    'intro' => 'Stoveitaly propose des poêles et des inserts à granulés aux façades vitrées élégantes, pour profiter de la flamme avec le confort d\'un chauffage automatique.',
    'content' => <<<'HTML'
<h2>Stoveitaly, le granulé en version vitrée</h2>
<p>Les appareils Stoveitaly se reconnaissent à leurs grandes façades en verre, qui mettent la flamme en valeur et s'intègrent facilement dans un intérieur contemporain.</p>

<h2>Les modèles présentés chez Braises Émeraude</h2>
<h3>Poêles à granulés</h3>
<p>Piano Glass, Gaio Glass, Compact Glass, Alaska Glass, Oto Plus et Duetto 9 : des poêles de formats variés, du plus compact au plus généreux. Voir nos <a href="/categorie-produit/chauffage-granule/poele/">poêles à granulés</a>.</p>
<h3>Inserts à granulés</h3>
<p>Piano, Gaio et l'insert vitré s'encastrent dans une cheminée existante pour la transformer en chauffage programmable. Voir nos <a href="/categorie-produit/chauffage-granule/insert/">inserts à granulés</a>.</p>

<h2>Les avantages du granulé</h2>
<ul>
<li>Allumage programmé et température régulée.</li>
<li>Autonomie de plusieurs heures à plusieurs jours selon le réservoir.</li>
<li>Combustible propre, régulier et facile à stocker.</li>
</ul>

<p>Nos conseillers vous aident à choisir la bonne puissance et assurent la pose et l'entretien. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'sergio-leoni' => [
    'h1' => 'Poêles Sergio Leoni',
    'seo_title' => 'Poêles à granulés Sergio Leoni',
    'seo_description' => 'Poêles à granulés Sergio Leoni (Marlene, Maria Costanza, Sissy) aux lignes raffinées : conseil et pose par Braises Émeraude.',
    'intro' => 'Lignes raffinées, finitions soignées : les poêles à granulés Sergio Leoni s\'adressent à ceux qui veulent un appareil aussi beau qu\'efficace.',
    'content' => <<<'HTML'
<h2>Sergio Leoni, le poêle à granulés côté design</h2>
<p>Les poêles Sergio Leoni misent sur l'élégance : formes arrondies, habillages travaillés, finitions qui en font de véritables meubles. Sous cette apparence, on retrouve le confort du granulé : allumage programmé, régulation de la température et autonomie.</p>

<h2>Les modèles chez Braises Émeraude</h2>
<p>Notre sélection comprend notamment Marlene, Marlene Tonda, Maria Costanza et Sissy, dans plusieurs puissances pour s'adapter à la taille de votre pièce.</p>
<p>Retrouvez-les dans nos <a href="/categorie-produit/chauffage-granule/poele/">poêles à granulés</a>.</p>

<h2>Choisir son modèle</h2>
<ul>
<li>La puissance, à adapter à la surface et à l'isolation.</li>
<li>Le style et la finition, pour s'accorder à votre intérieur.</li>
<li>L'emplacement et le type d'évacuation possible.</li>
</ul>

<p>Nos équipes vous conseillent, posent votre poêle et assurent son entretien annuel. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'jide' => [
    'h1' => 'Foyers et poêles Jidé',
    'seo_title' => 'Foyers, inserts et poêles à bois Jidé',
    'seo_description' => 'Foyers et inserts à bois Jidé (Pure, Loft, Lodge, Nordic, Décor, Corner) et poêle Modul\'Art : conseil et pose par Braises Émeraude.',
    'intro' => 'Jidé propose des foyers et inserts à bois aux styles variés, du plus épuré au plus chaleureux, ainsi que le poêle Modul\'Art.',
    'content' => <<<'HTML'
<h2>Jidé, des foyers pour toutes les cheminées</h2>
<p>La gamme Jidé couvre de nombreux styles de cheminées. Que vous rêviez d'un foyer minimaliste encastré dans un mur blanc ou d'une cheminée chaleureuse de chalet, il existe un modèle adapté.</p>

<h2>Les modèles présentés chez Braises Émeraude</h2>
<h3>Les foyers à bois</h3>
<ul>
<li><strong>Pure</strong> : des lignes épurées pour les intérieurs contemporains.</li>
<li><strong>Loft</strong> : un style plus affirmé, dans l'esprit industriel.</li>
<li><strong>Lodge</strong> : l'ambiance chaleureuse d'une cheminée de montagne.</li>
<li><strong>Nordic Plus</strong> et <strong>Décor Plus</strong> : deux autres interprétations du foyer à bois.</li>
</ul>
<h3>L'insert Corner</h3>
<p>Un insert à vision d'angle qui met la flamme en valeur sur deux côtés.</p>
<h3>Le poêle Modul'Art</h3>
<p>Un poêle à bois dont le design se compose comme un élément de mobilier.</p>

<p>Retrouvez ces appareils dans nos <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts et foyers à bois</a> et nos <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a>. Nos équipes réalisent l'étude, la pose et l'entretien. <a href="/devis/">Demandez votre devis gratuit</a>.</p>
HTML,
],

'rizzoli' => [
    'country' => 'Italie',
    'h1' => 'Cuisinières à bois Rizzoli',
    'seo_title' => 'Cuisinières à bois Rizzoli',
    'seo_description' => 'Cuisinières à bois italiennes Rizzoli, séries L, M, N, R, S et ZVI : chauffer et cuisiner au feu de bois avec Braises Émeraude.',
    'intro' => 'Fabricant italien spécialisé, Rizzoli conçoit des cuisinières à bois qui chauffent la maison tout en offrant four et plaque de cuisson.',
    'content' => <<<'HTML'
<h2>Rizzoli, l'art de cuisiner au bois</h2>
<p>La cuisinière à bois est un appareil à part : elle doit chauffer, cuire et durer. Rizzoli s'est spécialisé dans ce métier et propose des cuisinières qui associent un grand foyer, un four et une plaque de cuisson, dans des finitions pensées pour la cuisine.</p>

<h2>Les séries présentées chez Braises Émeraude</h2>
<p>Nous proposons plusieurs séries de la marque : L, M, N, R, S et ZVI. Elles se distinguent par leurs dimensions, leur puissance, la taille du four et leur style, du plus traditionnel au plus contemporain. Votre conseiller vous aide à choisir selon la taille de votre cuisine et votre façon de cuisiner.</p>

<h2>Les atouts d'une cuisinière à bois</h2>
<ul>
<li>Chauffer la cuisine, voire une grande partie de la maison.</li>
<li>Cuisiner sur la plaque et au four avec la même flambée.</li>
<li>Profiter d'une chaleur douce et d'un appareil qui dure.</li>
</ul>

<h2>Installation</h2>
<p>Une cuisinière se raccorde à un conduit comme un poêle. Nos équipes vérifient le conduit et l'emplacement, puis assurent la pose et l'entretien.</p>

<p>Découvrez notre rubrique <a href="/categorie-produit/cuisson/">cuisson</a> et <a href="/devis/">demandez votre devis gratuit</a>.</p>
HTML,
],

],

// ═══════════════════════════════════════════════ MAGASINS (clé = slug)
'stores' => [

'poele-a-bois-granules-dinard' => [
    'eyebrow' => 'Côte d\'Émeraude · Rive gauche de la Rance',
    'seo_title' => 'Poêle à bois et granulés à Dinard · Pleurtuit',
    'seo_description' => 'Braises Émeraude Dinard · Pleurtuit : showroom poêles à bois, à granulés et inserts. Conseil, pose et entretien sur la Côte d\'Émeraude.',
    'intro' => 'Notre showroom de Dinard · Pleurtuit accompagne les habitants de la rive gauche de la Rance : Dinard, Pleurtuit, Saint-Lunaire, Saint-Briac et les communes voisines. Poêles à bois, à granulés, inserts et foyers, en fonctionnement et avec conseil personnalisé.',
    'content' => <<<'HTML'
<h2>Votre spécialiste du chauffage au bois sur la Côte d'Émeraude</h2>
<p>Entre la Rance et la mer, la Côte d'Émeraude a ses propres contraintes : un climat doux mais humide, des maisons exposées au vent, des villas balnéaires aux grands volumes et de nombreuses résidences secondaires. Notre équipe de Dinard · Pleurtuit connaît ces maisons et vous conseille l'appareil adapté, de la petite maison de bourg à la grande villa.</p>

<h2>Ce que vous trouverez dans notre showroom</h2>
<ul>
<li><strong>Des <a href="/categorie-produit/chauffage-bois/poele-chauffage-bois/">poêles à bois</a></strong> en fonte, en acier ou à accumulation.</li>
<li><strong>Des <a href="/categorie-produit/chauffage-granule/">poêles et inserts à granulés</a></strong>, programmables et pilotables à distance.</li>
<li><strong>Des <a href="/categorie-produit/chauffage-mixte/">poêles mixtes</a></strong> bois et granulés.</li>
<li><strong>Des <a href="/categorie-produit/chauffage-bois/insert-chauffage-bois/">inserts et foyers</a></strong> pour rénover ou créer une cheminée.</li>
</ul>
<p>Plusieurs appareils fonctionnent en permanence pendant la saison de chauffe : la meilleure façon de comparer les flammes, la chaleur et les finitions.</p>

<h2>Résidences secondaires : le chauffage pilotable à distance</h2>
<p>Sur la côte, beaucoup de maisons ne sont pas occupées toute l'année. Un poêle à granulés connecté permet de lancer le chauffage depuis son téléphone avant d'arriver, de maintenir une température hors gel pendant l'absence et de limiter l'humidité. Une solution très adaptée aux maisons de Dinard, Saint-Lunaire et Saint-Briac.</p>

<h2>Maisons anciennes et villas balnéaires</h2>
<p>Les villas de Dinard et les maisons en pierre de la Rance ont souvent de grands volumes, des plafonds hauts et des cheminées d'origine. Un insert ou un foyer fermé permet de redonner vie à ces cheminées en les transformant en véritable chauffage. Un poêle à accumulation, lui, restitue une chaleur régulière dans les grandes pièces. Nos techniciens étudient le conduit existant et proposent, si besoin, un tubage conforme.</p>

<h2>Notre accompagnement, de l'étude à l'entretien</h2>
<ol>
<li><strong>Conseil en showroom</strong> ou par téléphone, pour cerner votre projet.</li>
<li><strong>Visite technique</strong> à domicile : surface, isolation, conduit, emplacement.</li>
<li><strong>Devis détaillé</strong>, pose comprise, avec les aides possibles. Voir <a href="/aides-financement/">aides et financement</a>.</li>
<li><strong>Installation</strong> par notre équipe et mise en service.</li>
<li><strong>Entretien et ramonage</strong> saison après saison.</li>
</ol>

<h2>Les communes que nous servons</h2>
<p>Dinard, Pleurtuit, Saint-Lunaire, Saint-Briac-sur-Mer, La Richardais, Le Minihic-sur-Rance et, côté Côtes-d'Armor, Lancieux et les communes voisines. Pour Saint-Malo, Cancale et le pays de Dol, notre showroom de <a href="/poele-a-bois-granules-saint-malo/">Saint-Malo · Baguer-Pican</a> prend le relais ; pour la métropole, rendez-vous à <a href="/poele-a-bois-granules-rennes/">Rennes</a>.</p>

<h2>Un projet de poêle à Dinard ?</h2>
<p>Passez nous voir au showroom ou <a href="/devis/">demandez votre devis gratuit</a> en ligne : votre demande arrive directement à l'équipe de Dinard · Pleurtuit.</p>
HTML,
],

],

];
