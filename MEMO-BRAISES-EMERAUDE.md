# MEMO technique — Braises Émeraude (refonte Laravel)

*Mis à jour le 23/10/2026*

## Identité
- Client : La Soleillade (SARL) — marque Braises Émeraude, 3 magasins (Dinard·Pleurtuit, Saint-Malo·Baguer-Pican, Rennes nouveau)
- Site actuel : WordPress + WooCommerce (catalogue), https://www.braisesemeraude.com
- Stack : Laravel 11, Filament v3.2, MySQL, PHP 8.3, O2Switch
- Maquette retenue pour démarrer : **Contrastes** (noir à reflet vert, émeraude `#34e3a5`, braise `#ff7b30`, DM Serif Display + Manrope + Caveat)
- Dépôt GitHub : `hervaultjeanmichel-sys/braises-emeraude` (privé, branche main)
- BDD prévue : `nafa4741_braises`

## Architecture
- Tables : stores, brands, brand_store, product_categories (path calculé), products, pages (sections JSON = Builder Filament), posts, cities, aid_topics, settings (colonne `key`), event_banners, redirects, form_options, leads, lead_notes ; users + `store_id`, `is_admin`
- Textes globaux : helper `s('cle', 'défaut')` → table settings (cache `settings.all`, vidé à l'enregistrement)
- Middleware `LegacyUrls` (prepend) : 301 de la table redirects (y compris `/?page_id=82`) puis slash final imposé
- `.htaccess` : règle Laravel « suppression du slash final » **retirée** ; https + www forcés sur le domaine de prod uniquement
- Résolution `/{slug}/` : Page → Store → City
- Robots : `noindex,nofollow` automatique hors `APP_ENV=production` ; robots.txt dynamique (Disallow total hors prod)
- Demandes : routage auto vers un magasin via `served_postcodes` (Store::forPostalCode), équipes magasin limitées à leurs demandes dans l'admin

## Commandes
- `php artisan braises:import-wordpress [--dry-run] [--only=products] [--force] [--no-images]` — API REST WP + Store API WooCommerce, images → WebP q82, Yoast → seo_title/description, ne remplit que les champs vides
- `php artisan braises:check-urls https://… [--user= --pass=]` — teste `database/data/legacy-urls.txt` (149 URLs)

## Points d'attention
- Code écrit sans `composer install` (Packagist inaccessible depuis l'espace de travail de Claude) → premier `migrate --seed` à surveiller : `php artisan log:clear` puis lire `storage/logs/laravel.log`
- Inventaire : 107 slugs produits listés (et non 106 comme indiqué dans la synthèse)
- 3 slugs produits nettoyés + 301 : jazz-mural-turbo-fonte(-copie), club-comfort-air-12-m2(-copie), cuisiniere-riz(z)oli-serie-m
- Cancale = page ville (rattachée à Saint-Malo) ; Rennes et Saint-Malo = pages magasins ; Dinard = nouvelle page magasin
- Coordonnées magasins provisoires (maquette) : adresse Saint-Malo indique « Baguer-Morvan » alors que le magasin s'appelle « Baguer-Pican » → à confirmer
- Marques/catégories des produits devinées depuis le slug, corrigées par l'import puis dans l'admin

## Avancement (6 étapes du cahier des charges)
1. Cadrage — en attente des réponses client (OpenFire, coordonnées, gaz, labels)
2. **BDD + admin — fait (23/10/2026), à installer sur la préprod**
3. Front — squelette fonctionnel + CSS de base ; design Contrastes complet à faire
4. Reprise contenus/URLs — seeders + import WP prêts
5. Connexions — colonnes prêtes (openfire_id, google_calendar_id, ai_*), code à faire
6. Mise en ligne — à venir
