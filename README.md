# Braises Émeraude — refonte Laravel

Laravel 11 + Filament v3 · MySQL · O2Switch · URLs WordPress conservées à l'identique.

## Mise en place sur la préprod O2Switch

À coller dans le terminal SSH, dans l'ordre. Remplacer `PREPROD` par le dossier de la préprod.

```bash
# 1. Envoyer le code (zip déposé dans ~/ via le gestionnaire de fichiers cPanel)
cd /home/nafa4741 && mkdir -p PREPROD && cd PREPROD
unzip -o ~/braises-emeraude.zip -d . && ls

# 2. Dépendances
composer install --no-dev --optimize-autoloader

# 3. Environnement
cp .env.example .env
php artisan key:generate
# Renseigner dans .env : DB_PASSWORD, APP_URL (adresse de la préprod), ADMIN_EMAIL, ADMIN_PASSWORD, APP_ENV=staging
python3 - <<'EOF'
p='.env'; s=open(p).read()
s=s.replace('APP_ENV=local','APP_ENV=staging').replace('APP_DEBUG=true','APP_DEBUG=false')
open(p,'w').write(s); print("OK — .env")
EOF

# 4. Base de données + données de reprise (~149 URLs)
php artisan migrate --seed --force
php artisan storage:link

# 5. Contenus WordPress (simulation d'abord)
php artisan braises:import-wordpress --dry-run
php artisan braises:import-wordpress

# 6. Caches
php artisan view:clear && php artisan cache:clear && php artisan config:cache && php artisan route:cache
touch public/index.php

# 7. Recette des anciennes URLs (200 ou 301 attendus partout)
php artisan braises:check-urls https://ADRESSE-PREPROD --user=USER_HTPASSWD --pass=MDP
```

Le document root du sous-domaine de préprod doit pointer sur `PREPROD/public`.

## Ce qui est en place

### Étape 3 — Front « Contrastes » (23/10/2026)

| Page | Contenu |
|---|---|
| Accueil Groupe | Bandeau cinéma 3 épisodes (scènes animées ou vidéo), 3 cartes magasins (ouvert/fermé en temps réel), univers, signature de campagne en parallaxe, bandeau marques, méthode, aides, texte SEO + FAQ, appel final — **chaque bloc est éditable et réordonnable** dans Pages › Accueil |
| Magasins | Page complète : texte local, horaires (jour en cours surligné), itinéraire, photos, équipe, marques exposées, communes |
| Catalogue | Catégories avec filtres marque / énergie (noindex sur les listes filtrées), fiches produits avec galerie, chiffres clés, caractéristiques |
| Mon projet | Bouton « Ajouter à mon projet » partout, compteur dans l'en-tête, page /mon-projet/ avec remarques, envoi au devis |
| Devis | /devis/ en 8 étapes (barre de progression, étape « remplacement » conditionnelle, magasin proposé selon le code postal, photos), fonctionne aussi sans JavaScript |
| Demandes | Enregistrées en base (source Google / réseaux / campagne Rennes), e-mail au magasin, anti-robots, 6 envois/min max |

Textes : **147 libellés** (menus, boutons, étapes du devis…) dans Réglages › Textes du site. Titres : `*mot*` = mot en émeraude.
Contrôle des gabarits sur le serveur : `php artisan view:cache` (compile toutes les vues).

### Étape 2 — BDD et admin

| Domaine | Contenu |
|---|---|
| Base | 15 tables : magasins, marques, catégories, produits, pages, articles, villes, aides, demandes, notes, listes du formulaire, réglages, bandeaux, redirections |
| Admin `/admin` | 14 rubriques Filament, champs SEO partout, compteur de mots (600 mini pages locales), équipes limitées à leur magasin |
| URLs | `/slug/`, `/produit/slug/`, `/categorie-produit/…/`, `/AAAA/MM/JJ/slug/`, `/marque/slug/`, `/aides/slug/` ; slash final imposé ; 301 gérées dans l'admin |
| SEO | title, description, canonical, robots (noindex hors production), Organization + 3 LocalBusiness, BreadcrumbList, Product, FAQPage, BlogPosting, sitemap.xml, robots.txt |
| Outils | `braises:import-wordpress` (textes, images en WebP, Yoast), `braises:check-urls` |

## Ordre de résolution d'une URL `/{slug}/`

Page CMS → page magasin → page ville → 404. Les redirections de la table `redirects` passent avant tout.

## Prochaines étapes

5. Connexions : OpenFire (API ou CSV), Google Agenda par magasin, IA Claude dans l'admin.
