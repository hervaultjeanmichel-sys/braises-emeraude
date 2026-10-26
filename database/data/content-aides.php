<?php

declare(strict_types=1);

/*
 * Contenus — lot 2 (26/10/2026) : aides au chauffage bois, vérifiées le 26/10/2026.
 * Fait clé : depuis le 1er septembre 2026, MaPrimeRénov' « par geste » ne finance plus
 * les équipements de chauffage indépendants au bois (poêles, inserts, foyers).
 * Source officielle : economie.gouv.fr (page du 28/08/2026).
 * Utilisé par la commande braises:fix-aides.
 */

$verified = '2026-10-26';
$srcMpr = 'https://www.economie.gouv.fr/particuliers/faire-des-economies-denergie/maprimerenov-parcours-par-geste-la-prime-pour-la-renovation-energetique';

return [

// Phrase de remplacement dans les anciens textes (magasins, ville, articles, accueil)
'replacement' => 'Depuis le 1er septembre 2026, MaPrimeRénov’ ne finance plus l’installation d’un poêle ou d’un insert seul ; votre projet peut en revanche bénéficier de la prime CEE, de la TVA à 5,5 % et de l’éco-prêt à taux zéro, sous conditions (voir nos <a href="/aides-financement/">aides et financement</a>).',

// ═══════════════════════════════════════ Page /aides-financement/ (réécrite entièrement)
'page' => [
    'eyebrow' => 'Aides & financement',
    'h1' => 'Aides pour un poêle à bois ou à granulés en 2026',
    'seo_title' => 'Aides poêle à bois et granulés 2026 : ce qui reste',
    'seo_description' => 'MaPrimeRénov’ ne finance plus les poêles depuis le 1er septembre 2026. Prime CEE, TVA à 5,5 %, éco-PTZ : les aides qui restent, vérifiées.',
    'intro' => 'Les aides au chauffage au bois ont beaucoup changé en 2026. Voici, vérifié par nos soins, ce qui finance encore l’installation d’un poêle, d’un insert ou d’un foyer, et ce qui ne le finance plus.',
    'content' => <<<'HTML'
<p><em>Informations vérifiées le 26 octobre 2026 auprès des sources officielles. Les aides évoluent souvent : nous refaisons le point avec vous à chaque devis.</em></p>

<h2>Ce qui a changé le 1er septembre 2026</h2>
<p>Depuis le <strong>1er septembre 2026</strong>, <strong>MaPrimeRénov’ « par geste »</strong> ne prend plus en charge les équipements de chauffage indépendants fonctionnant au bois : poêles à bûches, poêles à granulés, inserts et foyers fermés. Seules les demandes déposées auprès de l’Anah avant cette date restent instruites selon les anciennes règles.</p>
<p>Méfiez-vous donc des offres qui promettent encore MaPrimeRénov’ pour un poêle posé seul. <a href="/aides/maprimerenov-poele/">Tout savoir sur ce changement</a>.</p>

<h2>Les aides qui restent pour votre poêle</h2>
<h3>La prime CEE (certificats d’économies d’énergie)</h3>
<p>Versée par les fournisseurs d’énergie, elle concerne le remplacement ou l’installation d’un appareil performant. Son montant, de l’ordre de quelques centaines d’euros, dépend de vos revenus, de la zone climatique et de l’offre choisie. Point essentiel : l’offre doit être acceptée <strong>avant</strong> la signature du devis. <a href="/aides/prime-cee-poele/">En savoir plus sur la prime CEE</a>.</p>
<h3>La TVA à 5,5 %</h3>
<p>Quand l’appareil est fourni et posé par une entreprise dans un logement achevé depuis plus de deux ans, et qu’il respecte les critères de performance, la TVA est réduite à 5,5 % au lieu de 20 %. C’est souvent l’aide la plus importante en valeur. <a href="/aides/tva-5-5-poele/">Les conditions de la TVA réduite</a>.</p>
<h3>L’éco-prêt à taux zéro</h3>
<p>Il finance sans intérêts le reste à charge, jusqu’à 15 000 € pour une action seule. Il est disponible jusqu’au 31 décembre 2027 et se cumule avec la prime CEE et la TVA réduite. <a href="/aides/eco-ptz-poele/">Comment obtenir l’éco-PTZ</a>.</p>

<h2>Et dans le cadre d’une rénovation globale ?</h2>
<p><strong>MaPrimeRénov’ « rénovation d’ampleur »</strong> reste ouverte aux projets qui font gagner au moins deux classes au diagnostic de performance énergétique, avec un accompagnateur agréé. Le chauffage peut faire partie de ce bouquet de travaux : votre conseiller France Rénov’ vous confirmera ce qui est pris en compte. Ce parcours ne se cumule pas avec la prime CEE.</p>

<h2>Pourquoi passer par un professionnel qualifié</h2>
<p>La prime CEE et l’éco-PTZ exigent une installation réalisée par une entreprise qualifiée RGE. La TVA à 5,5 % suppose que l’appareil soit fourni et posé par l’entreprise. Un appareil acheté seul et posé soi-même ne donne droit à aucune de ces aides.</p>

<h2>Nos conseils pour bien monter votre dossier</h2>
<ol>
<li>Demandez votre <a href="/devis/">devis gratuit</a> : nous identifions les aides auxquelles vous avez droit.</li>
<li>Comparez et acceptez une offre de prime CEE <strong>avant</strong> de signer le devis.</li>
<li>Si besoin, sollicitez l’éco-PTZ auprès de votre banque avec le devis signé.</li>
<li>Après la pose, conservez votre facture : elle sert de justificatif pour chaque aide.</li>
</ol>

<h2>Aides locales</h2>
<p>Certaines collectivités proposent des primes pour remplacer une cheminée ouverte ou un vieil appareil au bois. Nous n’en avons pas identifié en Ille-et-Vilaine à ce jour ; renseignez-vous auprès de l’espace France Rénov’ de votre territoire, et nous mettrons cette page à jour dès qu’une aide locale s’ouvre.</p>
HTML,
    'faq' => [
        ['question' => 'Peut-on encore avoir MaPrimeRénov’ pour un poêle à granulés ?', 'answer' => 'Non, pas pour un poêle posé seul : depuis le 1er septembre 2026, MaPrimeRénov’ « par geste » ne finance plus les appareils de chauffage indépendants au bois. Il reste la prime CEE, la TVA à 5,5 % et l’éco-PTZ.'],
        ['question' => 'Les aides sont-elles cumulables ?', 'answer' => 'Oui : la prime CEE, la TVA à 5,5 % et l’éco-PTZ se cumulent. En revanche, MaPrimeRénov’ « rénovation d’ampleur » ne se cumule pas avec la prime CEE.'],
        ['question' => 'Quand faut-il demander la prime CEE ?', 'answer' => 'Avant de signer le devis. Une offre acceptée après la signature n’est plus valable.'],
        ['question' => 'Le crédit d’impôt existe-t-il encore ?', 'answer' => 'Non. Le crédit d’impôt pour la transition énergétique (CITE) a été remplacé par MaPrimeRénov’ en 2020 et a disparu depuis.'],
        ['question' => 'Les aides concernent-elles les résidences secondaires ?', 'answer' => 'La TVA à 5,5 % s’applique aux logements achevés depuis plus de deux ans, résidence principale ou secondaire. Pour la prime CEE et l’éco-PTZ, les conditions dépendent de l’offre et du prêt : nous vérifions votre cas lors du devis.'],
    ],
],

// ═══════════════════════════════════════ Fiches /aides/{slug}/
'topics' => [

[
    'slug' => 'maprimerenov-poele', 'sort' => 1,
    'title' => 'MaPrimeRénov’ et poêle à bois : ce qui a changé',
    'eyebrow' => 'Aides & financement',
    'h1' => 'MaPrimeRénov’ et poêle : la fin de l’aide par geste',
    'intro' => 'Depuis le 1er septembre 2026, MaPrimeRénov’ « par geste » ne finance plus l’installation d’un poêle, d’un insert ou d’un foyer au bois. Explications et solutions.',
    'seo_title' => 'MaPrimeRénov’ poêle : fin de l’aide en 2026',
    'seo_description' => 'MaPrimeRénov’ par geste ne finance plus les poêles et inserts depuis le 1er septembre 2026. Ce qui change et les aides qui restent.',
    'verified_on' => $verified,
    'source_url' => $srcMpr,
    'amounts' => [
        ['label' => 'Poêle, insert ou foyer posé seul', 'amount' => 'Plus éligible', 'condition' => 'Demandes déposées depuis le 1er septembre 2026'],
        ['label' => 'Rénovation d’ampleur', 'amount' => 'Possible', 'condition' => 'Gain d’au moins 2 classes DPE, avec accompagnateur'],
    ],
    'content' => <<<'HTML'
<h2>Ce que dit la réglementation</h2>
<p>Depuis le <strong>1er septembre 2026</strong>, les « équipements de chauffage ou de fourniture d’eau chaude sanitaire indépendants fonctionnant au bois ou autres biomasses » ne sont plus pris en compte dans MaPrimeRénov’ « parcours par geste ». Cela concerne les poêles à bûches, les poêles à granulés, les inserts et les foyers fermés.</p>
<p>Ce changement s’ajoute à la sortie des chaudières à bois du dispositif au 1er janvier 2026.</p>

<h2>Mon dossier était en cours : que se passe-t-il ?</h2>
<p>C’est la date de dépôt de la demande auprès de l’Anah qui compte, et non celle du devis. Une demande déposée avant le 1er septembre 2026 reste instruite selon les règles précédentes.</p>

<h2>MaPrimeRénov’ « rénovation d’ampleur »</h2>
<p>Le parcours « rénovation d’ampleur » reste ouvert aux logements qui gagnent au moins deux classes au DPE grâce à un bouquet de travaux, avec l’appui d’un accompagnateur Rénov’ agréé. Le remplacement du chauffage peut faire partie du projet : faites confirmer par votre conseiller France Rénov’ ce qui est retenu avant de signer.</p>

<h2>Les aides qui restent pour un poêle</h2>
<ul>
<li><a href="/aides/prime-cee-poele/">La prime CEE</a>, versée par les fournisseurs d’énergie.</li>
<li><a href="/aides/tva-5-5-poele/">La TVA à 5,5 %</a> sur l’appareil et la pose.</li>
<li><a href="/aides/eco-ptz-poele/">L’éco-prêt à taux zéro</a> pour financer le reste à charge.</li>
</ul>
<p>Lors de votre <a href="/devis/">devis gratuit</a>, nous faisons le point sur les aides auxquelles vous avez droit.</p>
HTML,
    'faq' => [
        ['question' => 'MaPrimeRénov’ peut-elle revenir pour les poêles ?', 'answer' => 'Nous suivons l’évolution des textes et mettrons cette page à jour en cas de changement. La date de vérification est indiquée en haut de page.'],
        ['question' => 'Un poêle mixte bois-granulés est-il concerné ?', 'answer' => 'Oui, il s’agit aussi d’un équipement de chauffage indépendant au bois.'],
    ],
],

[
    'slug' => 'prime-cee-poele', 'sort' => 2,
    'title' => 'Prime CEE pour un poêle à bois ou à granulés',
    'eyebrow' => 'Aides & financement',
    'h1' => 'La prime CEE pour votre poêle',
    'intro' => 'La prime « certificats d’économies d’énergie » est aujourd’hui la principale prime pour installer un poêle, un insert ou un foyer performant.',
    'seo_title' => 'Prime CEE poêle à bois et granulés 2026',
    'seo_description' => 'Prime CEE pour un poêle, un insert ou un foyer : principe, conditions, démarches. À demander avant de signer le devis.',
    'verified_on' => $verified,
    'source_url' => 'https://www.ecologie.gouv.fr/politiques-publiques/dispositif-certificats-deconomies-denergie',
    'amounts' => [
        ['label' => 'Montant', 'amount' => 'Quelques centaines d’euros', 'condition' => 'Selon revenus, zone climatique et offre du fournisseur'],
        ['label' => 'Ménages aux revenus modestes', 'amount' => 'Montant majoré', 'condition' => 'Sous conditions de ressources'],
    ],
    'content' => <<<'HTML'
<h2>Le principe</h2>
<p>Les fournisseurs d’énergie ont l’obligation de financer des économies d’énergie chez les particuliers. Ils le font en versant une prime lorsque vous installez un équipement performant, comme un poêle à bois, un poêle à granulés, un insert ou un foyer fermé répondant aux critères requis.</p>

<h2>Combien pouvez-vous toucher ?</h2>
<p>Le montant dépend de la zone climatique de votre logement, des performances de l’appareil, de vos revenus et du fournisseur qui achète vos certificats. Il n’existe plus de montant minimal garanti : comparez les offres. Pour un poêle, la prime représente généralement quelques centaines d’euros, davantage pour les ménages aux revenus modestes.</p>

<h2>Les conditions</h2>
<ul>
<li>Un logement achevé depuis plus de deux ans.</li>
<li>Un appareil qui respecte les seuils de rendement et d’émissions fixés par la fiche CEE : nous vérifions l’éligibilité de chaque modèle.</li>
<li>Une installation par une entreprise qualifiée RGE.</li>
<li>Une offre de prime acceptée <strong>avant</strong> la signature du devis.</li>
</ul>

<h2>Les étapes</h2>
<ol>
<li>Faites établir votre devis.</li>
<li>Choisissez une offre de prime CEE et acceptez-la par écrit, avant de signer.</li>
<li>Signez le devis ; nous réalisons l’installation.</li>
<li>Envoyez la facture et les justificatifs demandés : la prime vous est versée.</li>
</ol>

<h2>Cumul</h2>
<p>La prime CEE se cumule avec la <a href="/aides/tva-5-5-poele/">TVA à 5,5 %</a> et l’<a href="/aides/eco-ptz-poele/">éco-PTZ</a>. Elle ne se cumule pas avec MaPrimeRénov’ « rénovation d’ampleur ».</p>
HTML,
    'faq' => [
        ['question' => 'J’ai déjà signé mon devis, puis-je encore demander la prime ?', 'answer' => 'En principe non : l’offre CEE doit être acceptée avant la signature du devis. Pensez-y dès le début de votre projet.'],
        ['question' => 'Le remplacement d’une vieille cheminée est-il mieux aidé ?', 'answer' => 'Certaines offres sont majorées lorsqu’un ancien appareil est remplacé. Nous vérifions votre cas lors du devis.'],
    ],
],

[
    'slug' => 'tva-5-5-poele', 'sort' => 3,
    'title' => 'TVA à 5,5 % pour un poêle à bois ou à granulés',
    'eyebrow' => 'Aides & financement',
    'h1' => 'TVA réduite à 5,5 % sur votre poêle',
    'intro' => 'Quand il est fourni et posé par un professionnel, votre poêle peut bénéficier d’une TVA à 5,5 % au lieu de 20 % : souvent l’économie la plus importante.',
    'seo_title' => 'TVA 5,5 % poêle à bois et granulés : conditions',
    'seo_description' => 'Poêle, insert ou foyer fourni et posé par une entreprise dans un logement de plus de 2 ans : TVA à 5,5 %. Conditions et exemples.',
    'verified_on' => $verified,
    'source_url' => 'https://www.impots.gouv.fr/particulier/questions/quel-taux-de-tva-sappliquent-aux-travaux-dans-mon-logement',
    'amounts' => [
        ['label' => 'Appareil + pose par une entreprise', 'amount' => '5,5 %', 'condition' => 'Logement achevé depuis plus de 2 ans, appareil performant'],
        ['label' => 'Appareil acheté seul', 'amount' => '20 %', 'condition' => 'Sans pose par une entreprise'],
    ],
    'content' => <<<'HTML'
<h2>Les conditions</h2>
<ul>
<li>Le logement est achevé depuis <strong>plus de deux ans</strong> (résidence principale ou secondaire).</li>
<li>L’appareil est <strong>fourni et posé par l’entreprise</strong> qui facture les travaux.</li>
<li>L’appareil respecte les critères de performance énergétique requis.</li>
</ul>
<p>La mention du taux réduit et de ses conditions figure sur le devis et la facture.</p>

<h2>Exemple</h2>
<p>Pour une installation facturée 8 000 € hors taxes, la TVA à 5,5 % représente 440 €, contre 1 600 € au taux normal de 20 % : une différence de 1 160 €.</p>

<h2>Ce qui n’en bénéficie pas</h2>
<ul>
<li>Un appareil acheté seul, sans pose par une entreprise : TVA à 20 %.</li>
<li>Un logement neuf ou achevé depuis moins de deux ans.</li>
<li>Le combustible : bûches et granulés relèvent d’autres taux.</li>
</ul>

<h2>Cumul</h2>
<p>La TVA réduite se cumule avec la <a href="/aides/prime-cee-poele/">prime CEE</a> et l’<a href="/aides/eco-ptz-poele/">éco-PTZ</a>. Nous l’appliquons directement sur votre devis lorsque les conditions sont réunies : <a href="/devis/">demandez le vôtre</a>.</p>
HTML,
    'faq' => [
        ['question' => 'Le conduit et le tubage sont-ils aussi à 5,5 % ?', 'answer' => 'Les travaux indissociables de la pose de l’appareil suivent en principe le même taux. Le détail figure sur votre devis.'],
    ],
],

[
    'slug' => 'eco-ptz-poele', 'sort' => 4,
    'title' => 'Éco-prêt à taux zéro pour un poêle',
    'eyebrow' => 'Aides & financement',
    'h1' => 'Financer son poêle avec l’éco-PTZ',
    'intro' => 'L’éco-prêt à taux zéro permet de financer l’installation de votre poêle sans payer d’intérêts, jusqu’à 15 000 € pour une action seule.',
    'seo_title' => 'Éco-PTZ poêle à bois et granulés : jusqu’à 15 000 €',
    'seo_description' => 'Financez votre poêle sans intérêts avec l’éco-PTZ : jusqu’à 15 000 € pour une action, jusqu’au 31 décembre 2027. Conditions et démarches.',
    'verified_on' => $verified,
    'source_url' => 'https://www.service-public.fr/particuliers/vosdroits/F19905',
    'amounts' => [
        ['label' => 'Une action (ex. : poêle)', 'amount' => 'Jusqu’à 15 000 €', 'condition' => 'Remboursable sur 15 ans maximum'],
        ['label' => 'Fin du dispositif', 'amount' => '31/12/2027', 'condition' => 'Offre de prêt à émettre avant cette date'],
    ],
    'content' => <<<'HTML'
<h2>Le principe</h2>
<p>L’éco-PTZ est un prêt sans intérêts, accordé par les banques partenaires, pour financer des travaux de rénovation énergétique. Il ne réduit pas le prix de votre poêle mais vous évite d’avancer la somme ou de payer les intérêts d’un crédit classique.</p>

<h2>Les montants</h2>
<ul>
<li>Jusqu’à <strong>15 000 €</strong> pour une action seule, comme l’installation d’un poêle.</li>
<li>Des plafonds plus élevés lorsque plusieurs travaux sont combinés.</li>
<li>Une durée de remboursement de 15 ans au maximum.</li>
</ul>

<h2>Les conditions</h2>
<ul>
<li>Être propriétaire d’un logement achevé depuis plus de deux ans, occupé ou loué en résidence principale.</li>
<li>Faire réaliser les travaux par une entreprise qualifiée RGE.</li>
<li>Présenter le devis à votre banque pour obtenir l’offre de prêt.</li>
</ul>

<h2>Cumul</h2>
<p>L’éco-PTZ se cumule avec la <a href="/aides/prime-cee-poele/">prime CEE</a> et la <a href="/aides/tva-5-5-poele/">TVA à 5,5 %</a>. Il permet souvent de financer le reste à charge après les aides.</p>
<p>Nous vous remettons un devis détaillé à présenter à votre banque : <a href="/devis/">demandez-le gratuitement</a>.</p>
HTML,
    'faq' => [
        ['question' => 'Toutes les banques proposent-elles l’éco-PTZ ?', 'answer' => 'Seules les banques ayant signé une convention avec l’État le distribuent. Renseignez-vous auprès de la vôtre.'],
        ['question' => 'L’éco-PTZ est-il possible pour une résidence secondaire ?', 'answer' => 'Non, le logement doit être une résidence principale, occupée par vous ou louée.'],
    ],
],

],

// ═══════════════════════════════════════ Corrections ciblées des anciens textes
// [motif regex, remplacement] appliqués dans l'ordre, sur tous les champs texte et JSON.
// «R» = la phrase de remplacement ci-dessus.
'rules' => [
    ['/Notre statut RGE et Qualibois vous permet de mobiliser Ma ?Prime ?R[ée]nov’? et d’autres dispositifs d’aide/u', 'Notre statut RGE et Qualibois vous permet de bénéficier de la prime CEE, de la TVA à 5,5 % et de l’éco-PTZ'],
    ['/En effet, cette prime permet de financer[^.<]*\.\s*/u', ''],
    ['/Cette prime de l.État finance une partie des travaux[^.<]*\.\s*/u', ''],
    ['/Le montant varie[^.<\[]*(?:\.|\[…\]|…)\s*/u', ''],
    ['/<(h[2-4])([^>]*)>(?:(?!<\/\1>).)*?(?:ma ?prime ?r[ée]nov|cr[ée]dit d.imp[ôo]t)(?:(?!<\/\1>).)*?<\/\1>/isu', '<$1$2>Les aides en vigueur</$1><p>«R»</p>'],
    ['/[^.!?<>|]*(?:ma ?prime ?r[ée]nov|cr[ée]dit d.imp[ôo]t)[^.!?<>]*[.!?]?/iu', '«R»'],
],

];
