# MEMO technique — Braises Émeraude (refonte Laravel)

*Mis à jour le 29/10/2026 (18h30)*

## 📍 Reste à faire (au 26/10/2026)
1. **Prise de RDV** : module fait et actif pour Rennes (27/10). Reste : vraie adresse e-mail de Rennes (provisoirement celle de JM), activer Dinard / Saint-Malo, branchement Google Agenda (compte de service)
2. **Fiches produits** : 99/107 sous 300 mots (une description courte chacune) → lot de contenu produits
3. **Page /chauffage/** : revoir la mise en page avec le contenu nettoyé (marques et produits remis le 26/10) ; message de JM coupé (« remettre les marques et… ») → à clarifier
3 bis. **Réalisations** : associer magasin + appareil + vrais avis clients (avec accord) aux 24 chantiers ; Plouasne à saisir sur le Gaio Glass
4. **Pages courtes** : cuisson, chauffage-mixte, boutique, réalisations, showroom, contact, politique de confidentialité (vide)
5. **Pages villes** : seule Cancale existe (Dinard, Dol, Pleurtuit, Combourg… à créer)
6. Vidéo d'accueil : montage MCZ possible si JM fournit les rushes (espace revendeur MCZ)
7. Questions client : gaz encore vendu ? RGE Qualibois toujours valide ? adresses magasins (Baguer-Morvan vs Baguer-Pican) ? droits vidéo MCZ ?
8. ⚠️ Le site WordPress EN LIGNE promet encore MaPrimeRénov' pour les poêles (faux depuis le 01/09/2026) → à corriger sur braisesemeraude.com sans attendre la refonte

## Session du 29/10/2026 — site plus lumineux (demande client)
- **Vidéo d'accueil** : nouveau réglage « Voile sombre » du bloc saga (`veil` : light / medium / dark, défaut **light**) → classes `.saga.veil-*` ; luminosité vidéo 1 au lieu de .72
- **Le Groupe** : réglage « Fond de la section » du bloc stores (`tone` : light = `#34e3d3` / deep = ancien émeraude profond), classe `.bg-emerald-light`, cartes blanches
- **Signature « au poil »** : réglage « Fond du texte » du bloc quote (`tone` : beige / dark), classe `.parallax.tone-beige`, variable `--gold-beige:#e8d4a6`
- **Volets promo** (glissent de gauche à droite) : table `promo_slides`, admin Contenus › Volets promo (image → WebP 1200 px, couleur de fond, sur-titre / titre / texte / bouton, début et fin d'affichage, pages : toutes ou accueil, fréquence session / jour / semaine / toujours, délai en s, ordre). `PromoSlide::current()`, texte clair ou foncé calculé selon la couleur de fond, `public/js/promo.js` (✕, Échap, mémorisé en localStorage/sessionStorage, clé = id + date de modif), partial `front/partials/promo-slide`
- Volets promo : colonne `position` (center par défaut = glisse jusqu'au centre avec fond assombri cliquable, ou bottom-left)
- **Zones magasins** : colonnes `stores.zone_label` / `zone_text` (admin Magasins › Coordonnées), remplies par la migration : Zone de Dinard (rive gauche de la Rance, ~30 km), Zone Saint-Malo (Saint-Malo, Cancale, jusqu'à Combourg), Zone Rennes (Combourg → Rennes, Vitré, Fougères, ~40 km). Carte d'accueil : titre = zone, sur-titre « Showroom à :ville » (`store.showroom_at`), étiquettes de la carte = zone. Ville du magasin Saint-Malo corrigée en **Baguer-Pican** (confirmé par JM le 29/10)
- **Cercles de zone** sur la carte de l'accueil : `stores.zone_radius_km` (Dinard 20, Saint-Malo 25, Rennes 30 km, réduits par JM le 29/10 ; vide = pas de cercle), dessinés dans `public/js/map.js`
- **Carte claire** par défaut (réglage `design.map_theme` = light | dark)
- ASSET_VERSION = 2.1

## Session du 26/10/2026 — fait
- **Accueil allégé** (commit 118f54d) : scènes canvas en sprites, résolution 1x, plus de pré-calcul au chargement → chargement test 26 s → 0,13 s (ordi), 55 s → 0,4 s (mobile). LCP mesuré par JM : 1,44 s en 4G lente, CLS 0
- **Vidéo de fond commune** aux 3 épisodes de la Saga : champs `bg_video` / `bg_poster` du bloc saga ; vidéo MCZ Eiko (1,1 Mo) dans `storage/app/public/accueil/mcz-eiko-loop.mp4` ; chargée après `load`, ignorée en économie de données
- **Import WordPress lancé** : 135 fiches complétées ; 149/149 anciennes URLs OK
- **Contenus lot 1** (`ContentLot1Seeder`, `database/data/content-lot1.php`) : 8 catégories, 11 marques, magasin Dinard — remplit seulement les champs vides
- **Aides 2026** (`braises:fix-aides`, `database/data/content-aides.php`) : MaPrimeRénov' par geste ne finance plus les poêles depuis le 01/09/2026 (economie.gouv.fr, 28/08/2026) → /aides-financement/ réécrite + 4 fiches /aides/{maprimerenov-poele, prime-cee-poele, tva-5-5-poele, eco-ptz-poele}/ (verified_on 26/10/2026) + 6 anciens textes corrigés
- **Nettoyage HTML WordPress** (`braises:clean-html`, `App\Support\HtmlCleaner`) : Divi/WPBakery retirés (1,08 M → 174 k caractères), 1 636 images rapatriées en WebP dans `storage/wp/`, liens internes relatifs, encart « L'expertise Braise Emeraude » dupliqué retiré, tableaux techniques → `products.specs` + `power_kw` (105 fiches)
- **Pages univers** : grille des marques (avec nb de modèles) + sélection de produits sur /chauffage/
- **Accueil** : l'ancien texte WordPress de la page / n'est plus affiché sous le bloc final (rendu seulement si aucun bloc)
- **Couleur** : émeraude alignée sur le logo `#34e3d3` (sections claires `#06756d`), admin Filament aussi
- **Cartes OpenStreetMap / Leaflet 1.9.4** (`public/js/map.js`, SRI vérifiés, chargées à l'approche, sans cookie) : page magasin, accueil (#groupe, interrupteur `show_map` du bloc magasins), page contact. `Store::mapMarker()`, `directionsUrl()` ; `addressLine()` inclut maintenant la ville
- **Coordonnées GPS** : `braises:geocode-stores [--apply] [--force]` (lien Google Business → sinon Nominatim). Les liens share.google mènent à une fiche de recherche, sans coordonnées → géocodage par adresse. Enregistrées le 26/10
- **Réalisations** : table `realisations`, admin Contenus › Réalisations (avis affiché seulement si `review_consent`), photos → WebP (`App\Support\WebpImage`), gabarit `pages/realisations` (filtres, visionneuse). `braises:realisations-from-page --publish-titled [--apply]` : 24 chantiers créés depuis l'ancienne page (légendes d'origine, commune + énergie déduites)
- **SSH GitHub** : port 22 refusé depuis O2Switch le 26/10 → alias `github-braises` passé sur `ssh.github.com:443` dans ~/.ssh/config
- ASSET_VERSION = 0.9

## Session du 27/10/2026 (suite) — design, effets, tableau de bord, avis Google
- **Cartes magasins** entièrement cliquables (lien étiré `.go::after`), téléphone toujours cliquable
- **Page magasin** : galerie 3–10 photos (WebP auto via `Store::booted`), visionneuse commune `public/js/gallery.js`, vidéo fichier ou YouTube/Vimeo chargée au clic (colonnes `video`, `video_url`, `video_poster`)
- **Emblème du logo** vectorisé : `public/img/emblem.svg` (retracé simplifié, 6,6 Ko — à remplacer par le vectoriel officiel si fourni) ; filigrane `.has-emblem` (interrupteur `emblem` par bloc), petit emblème devant les sur-titres (réglage `design.eyebrow_emblem`)
- **Effets saisonniers** : table `seasonal_effects` (+ calendrier : Halloween, Noël, Nouvel an, Saint-Valentin, Pâques 2027, Inauguration Rennes inactif), admin Contenus › Effets saisonniers, `public/js/effects.js` (8 effets, 30 i/s, bouton ✕, désactivé si « réduire les animations »)
- **Tableau de bord** : mesure d'audience sans cookie (`visits` empreinte journalière hachée, `page_views`, middleware `RecordVisit`), widgets LiveStats (30 s), SeasonalEffectCard, BookingByStore (interrupteur RDV), VisitsChart, TopPages, SiteHealth ; widgets sensibles réservés à l'admin groupe
- **Avis Google** : `App\Support\GoogleReviews` (Places API New, cache court `GOOGLE_REVIEWS_CACHE_MINUTES`, échec mémorisé 10 min), `stores.google_place_id`, table `google_review_choices`, page admin Magasins › Avis Google, bloc Builder `google_reviews`, avis du magasin sur sa page ; mentions obligatoires (date de visite, lien Google Maps, signalement, sélection). **En attente de la clé `GOOGLE_PLACES_KEY`** → puis `php artisan braises:google-reviews --find`
- **PDF client** « Braises-Emeraude-Projet-nouveau-site.pdf » (9 p., formulé en projet, sans prix ni planning) remis à JM
- ASSET_VERSION = 1.5

## Session du 27/10/2026 — prise de rendez-vous
- **Tables** : `appointments` (en_attente → confirme / refuse / annule) ; réglages par magasin dans `stores.booking_settings`
- **Réglages par magasin** (onglet Rendez-vous) : interrupteur général `booking_enabled` ; catégories ouvertes `allow_showroom` / `allow_home` / `allow_sweep` ; `home_radius_km` ; types `{label, kind: showroom|domicile|ramonage, duration, enabled}` ; plages `hours` (sinon horaires du magasin) ; `start_from` (calendrier), `showroom_from`, `min_delay_hours`, `max_days`, `step`, capacités
- **Calcul des créneaux** : `App\Support\Booking` (fuseau Europe/Paris, APP_TIMEZONE ajouté au .env) ; rayon via `App\Support\Geo` (Nominatim, cache 30 j, haversine) — adresse non localisée = acceptée avec « distance non vérifiée »
- **Parcours** : /rendez-vous/ (4 étapes, `public/js/booking.js`), /rendez-vous/creneaux (JSON), /rendez-vous/zone (JSON), /rendez-vous/merci/ ; bouton « Prendre rendez-vous » sur la page magasin
- **Validation** : admin Demandes › Rendez-vous, boutons Confirmer / Refuser → e-mails `App\Support\BookingMailer` (alerte magasin, accusé client, confirmation + .ics, refus)
- **Commande** : `php artisan braises:booking {magasin} [--enable] [--disable] [--from=] [--showroom-from=] [--radius=] [--showroom=on|off] [--home=on|off] [--sweep=on|off] [--email=] [--add-types]` (affiche aussi le diagnostic des créneaux)
- **Rennes (27/10)** : active, calendrier à partir du 15/11 (1er créneau 17/11), showroom + domicile + ramonage, rayon 25 km, types « En Magasin » 15 min (saisi par JM), visite technique 90 min, ramonage 60 min
- **Bug corrigé** : `(new RedirectResponse(...))->with()` → erreur 500 (pas de session) sur devis / contact / RDV → `session()->flash()` puis `new RedirectResponse`
- ASSET_VERSION = 1.1

## Identité
- Client : La Soleillade (SARL) — marque Braises Émeraude, 3 magasins (Dinard·Pleurtuit, Saint-Malo·Baguer-Pican, Rennes nouveau)
- Site actuel : WordPress + WooCommerce (catalogue), https://www.braisesemeraude.com
- Stack : Laravel 12, Filament v3.3, MySQL, PHP 8.3, O2Switch
- Maquette retenue : **Contrastes** (noir à reflet vert, émeraude `#34e3d3` (bleu-vert du logo, depuis le 26/10 ; variante sections claires `#06756d`), braise `#ff7b30`, DM Serif Display + Manrope + Caveat)
- Dépôt GitHub : `hervaultjeanmichel-sys/braises-emeraude` (privé) — **on travaille sur `develop`** (= préprod) ; `main` = future prod

## Git & environnements
- Dépôt GitHub      : git@github.com:hervaultjeanmichel-sys/braises-emeraude.git (privé)
- Stack             : Laravel+Filament
- Préprod (develop) : https://braises.comabord.com — /home/nafa4741/preprod/braises/ (docroot : …/braises/public) — BDD nafa4741_braises_preprod — verrou PREPROD_USER/PREPROD_PASSWORD dans le .env
- Prod (main)       : https://www.braisesemeraude.com — à définir — BDD nafa4741_braises
- Dernier tag livré : —

## Architecture
- Tables : stores, brands, brand_store, product_categories (path calculé), products, pages (sections JSON = Builder Filament), posts, cities, aid_topics, settings (colonne `key`), event_banners, redirects, form_options, leads, lead_notes ; users + `store_id`, `is_admin`
- Textes globaux : helper `s('cle', 'défaut')` → table settings (cache `settings.all`, vidé à l'enregistrement)
- Middleware `LegacyUrls` (prepend) : 301 de la table redirects (y compris `/?page_id=82`) puis slash final imposé
- `.htaccess` : règle Laravel « suppression du slash final » **retirée** ; https + www forcés sur le domaine de prod uniquement
- Résolution `/{slug}/` : Page → Store → City
- Robots : `noindex,nofollow` automatique hors `APP_ENV=production` ; robots.txt dynamique (Disallow total hors prod)
- Demandes : routage auto vers un magasin via `served_postcodes` (Store::forPostalCode), équipes magasin limitées à leurs demandes dans l'admin

## Front (étape 3)
- Assets : `public/css/site.css`, `public/js/contrastes.js` (Saga, curseur, parallaxe, reveal, vidéo différée), `public/js/site.js` (menu mobile, galerie, Mon projet, devis) ; version `ASSET_VERSION` dans .env
- Images : `public/img/logo.webp`, `public/img/campagne-chien.webp`
- Accueil = Page slug `/` avec blocs Builder : saga, stores, categories, quote, marquee, steps, aids, seo_faq, final, text
- Pages univers (template `univers`) : catégories, contenu, produits, grille des marques
- Helpers : `s()` texte éditable, `em()` *mot* → <em>, `menu()` lignes « Libellé|/url/ », `tel()`, `media()`
- Textes : `database/data/settings-defaults.php` → SettingSeeder
- Mon projet : localStorage `be_project` → champ caché `products` → Lead.products
- Devis : `LeadController` (POST /devis/, /contact/, throttle 6/min, pot de miel `website`), photos en stockage privé `storage/app/demandes`, e-mail au magasin (repli : réglage `leads.fallback_email`)
- Provenance : middleware `TrackSource` (UTM + référent en session) → Lead.source

## Commandes
- `php artisan braises:import-wordpress [--dry-run] [--only=products] [--force] [--no-images]` — ne remplit que les champs vides
- `php artisan braises:check-urls https://… [--user= --pass=]` — teste les 149 anciennes URLs
- `php artisan db:seed --class=ContentLot1Seeder --force` — contenus catégories / marques / Dinard
- `php artisan braises:fix-aides [--apply]` — aides 2026 + correction MaPrimeRénov' / crédit d'impôt (simulation par défaut)
- `php artisan braises:clean-html [--show=slug] [--apply]` — nettoyage HTML WordPress + rapatriement images/PDF (simulation par défaut)
- Déploiement préprod : `cd /home/nafa4741/preprod/braises && git pull --ff-only origin develop && php artisan view:clear && php artisan cache:clear && touch public/index.php` (+ `ASSET_VERSION` dans .env si CSS/JS modifiés)

## Installation préprod (23/10/2026) — notes
- Clé SSH dédiée : `~/.ssh/id_braises` + alias `github-braises` (clé de déploiement lecture seule). Remote : `git@github-braises:hervaultjeanmichel-sys/braises-emeraude.git`
- Passage en Laravel 12 (composer.lock créé côté serveur, non versionné)
- Mots de passe du .env : utiliser `read -rsp`
- SSL : cPanel › Lets Encrypt™ SSL › Issue sur braises.comabord.com
- Pas de ffmpeg sur O2Switch

## Points d'attention
- **Ne jamais utiliser `redirect('/x/')` pour une URL publique** : utiliser `new RedirectResponse('/x/')`
- Le serveur préprod suit **develop** : toujours pousser sur develop puis `git pull --ff-only origin develop`
- Commit 118f54d avec l'e-mail de JM (« Unverified » sur GitHub) : volontairement non réécrit, déjà déployé
- **Blade** : jamais de directive collée à une lettre ou un chiffre (`/5@endif` → non reconnu → erreur 500). Mettre un espace : `/5 @endif`
- **Redirections** : ne jamais chaîner `->with()` sur `new RedirectResponse()` ; passer par `session()->flash()`
- Avis clients : uniquement de vrais avis, publiés avec l'accord du client (case dans l'admin) — jamais rédigés à sa place
- Copier-coller depuis le terminal SSH : les espaces disparaissent aux retours à la ligne (« plusl’installation ») → ce n'est pas un bug
- Le client affirme être « RGE et Qualibois » (texte de sa page Saint-Malo) : validité à confirmer
- Contenus : aucun fait non vérifié (pas de label, prix, origine incertaine de marque). Origine affichée seulement pour Austroflamm, Romotop, Dovre, Brunner, MCZ, Rizzoli
- 3 slugs produits nettoyés + 301 : jazz-mural-turbo-fonte(-copie), club-comfort-air-12-m2(-copie), cuisiniere-riz(z)oli-serie-m
- Cancale = page ville ; Rennes et Saint-Malo = pages magasins ; Dinard = nouvelle page magasin
- Magasin zone Saint-Malo : à Baguer-Pican (confirmé par JM le 29/10/2026)

## Avancement (6 étapes du cahier des charges)
1. Cadrage — en attente des réponses client (OpenFire, coordonnées, gaz, labels)
2. **BDD + admin — fait** (23/10/2026)
3. **Front Contrastes — fait** (23/10), accueil allégé + vidéo (26/10)
4. **Reprise contenus/URLs — en grande partie faite** (26/10) : import WP, nettoyage HTML, lot 1, aides 2026 ; restent produits, pages courtes, villes
5. Connexions — colonnes prêtes (openfire_id, google_calendar_id, ai_*), code à faire — **prise de RDV Google Agenda : prioritaire**
6. Mise en ligne — à venir
