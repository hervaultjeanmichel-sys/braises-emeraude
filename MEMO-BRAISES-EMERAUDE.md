# MEMO technique — Braises Émeraude (refonte Laravel)

*Mis à jour le 23/10/2026*

## Identité
- Client : La Soleillade (SARL) — marque Braises Émeraude, 3 magasins (Dinard·Pleurtuit, Saint-Malo·Baguer-Pican, Rennes nouveau)
- Site actuel : WordPress + WooCommerce (catalogue), https://www.braisesemeraude.com
- Stack : Laravel 12, Filament v3.3, MySQL, PHP 8.3, O2Switch
- Maquette retenue pour démarrer : **Contrastes** (noir à reflet vert, émeraude `#34e3a5`, braise `#ff7b30`, DM Serif Display + Manrope + Caveat)
- Dépôt GitHub : `hervaultjeanmichel-sys/braises-emeraude` (privé, branche main)

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
- Assets : `public/css/site.css` (CSS maquette v3 + ajouts), `public/js/contrastes.js` (scènes canvas, curseur flamme, parallaxe, reveal — repris de la maquette), `public/js/site.js` (menu mobile, galerie, Mon projet, devis) ; version `ASSET_VERSION` dans .env
- Images : `public/img/logo.webp`, `public/img/campagne-chien.webp` (extraites de la maquette)
- Accueil = Page slug `/` avec blocs Builder : saga, stores, categories, quote, marquee, steps, aids, seo_faq, final, text
- Helpers : `s()` texte éditable, `em()` *mot* → <em>, `menu()` lignes « Libellé|/url/ », `tel()`, `media()`
- Textes : `database/data/settings-defaults.php` (147 clés, généré depuis les gabarits) → SettingSeeder
- Mon projet : localStorage `be_project` → champ caché `products` → Lead.products (seuls les produits existants sont gardés)
- Devis : `LeadController` (POST /devis/, /contact/, throttle 6/min, pot de miel `website`), photos en stockage privé `storage/app/demandes`, e-mail au magasin (repli : réglage `leads.fallback_email`)
- Provenance : middleware `TrackSource` (UTM + référent en session) → Lead.source
- Vérifs faites : php -l, compilation des 27 vues avec le BladeCompiler Laravel 11, tests jsdom du devis et de Mon projet

## Commandes
- `php artisan braises:import-wordpress [--dry-run] [--only=products] [--force] [--no-images]` — API REST WP + Store API WooCommerce, images → WebP q82, Yoast → seo_title/description, ne remplit que les champs vides
- `php artisan braises:check-urls https://… [--user= --pass=]` — teste `database/data/legacy-urls.txt` (149 URLs)

## Installation préprod (23/10/2026) — notes
- Clé SSH dédiée sur le serveur : `~/.ssh/id_braises` + alias `github-braises` dans ~/.ssh/config (clé de déploiement lecture seule du dépôt). Remote : `git@github-braises:hervaultjeanmichel-sys/braises-emeraude.git`
- Laravel 11 bloqué par les alertes de sécurité Composer → passage en Laravel 12 (`composer update` sur le serveur, composer.lock créé côté serveur, non versionné pour l'instant)
- Mots de passe du .env : utiliser `read -rsp` (getpass dans un heredoc ne lit rien → valeur vide)
- SSL : cPanel › Lets Encrypt™ SSL › Issue sur braises.comabord.com

## Points d'attention
- **Ne jamais utiliser `redirect('/x/')` pour une URL publique** : le générateur d'URL Laravel retire le slash final → boucle avec LegacyUrls. Utiliser `new RedirectResponse('/x/')`
- Code écrit sans `composer install` (Packagist inaccessible depuis l'espace de travail de Claude) → premier `migrate --seed` à surveiller : `php artisan log:clear` puis lire `storage/logs/laravel.log`
- Inventaire : 107 slugs produits listés (et non 106 comme indiqué dans la synthèse)
- 3 slugs produits nettoyés + 301 : jazz-mural-turbo-fonte(-copie), club-comfort-air-12-m2(-copie), cuisiniere-riz(z)oli-serie-m
- Cancale = page ville (rattachée à Saint-Malo) ; Rennes et Saint-Malo = pages magasins ; Dinard = nouvelle page magasin
- Coordonnées magasins provisoires (maquette) : adresse Saint-Malo indique « Baguer-Morvan » alors que le magasin s'appelle « Baguer-Pican » → à confirmer
- Marques/catégories des produits devinées depuis le slug, corrigées par l'import puis dans l'admin

## Avancement (6 étapes du cahier des charges)
1. Cadrage — en attente des réponses client (OpenFire, coordonnées, gaz, labels)
2. **BDD + admin — fait et installé sur la préprod (23/10/2026)** : migrate --seed OK du premier coup, accueil rendu en 200
3. **Front Contrastes — fait (23/10/2026)** : accueil Groupe (blocs Builder), magasins, catalogue filtré, fiches, Mon projet, devis 8 étapes, contact
4. Reprise contenus/URLs — seeders + import WP prêts
5. Connexions — colonnes prêtes (openfire_id, google_calendar_id, ai_*), code à faire
6. Mise en ligne — à venir
