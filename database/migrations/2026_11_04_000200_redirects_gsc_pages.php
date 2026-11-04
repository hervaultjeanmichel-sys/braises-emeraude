<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* Mise en ligne 04/11/2026 : anciennes URLs WordPress vues dans Search Console (3 derniers mois) et absentes de l'inventaire.
 * + réglage seo.google_site_verification (balise HTML de validation Search Console, modifiable dans l'admin). */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $map = [
            '/etiquette-produit/miniac-morvan/' => ['/poele-a-bois-granules-saint-malo/', 301],
            '/etiquette-produit/pleudihen/' => ['/poele-a-bois-granules-dinard/', 301],
            '/etiquette-produit/poele-a-bois/page/2/' => ['/categorie-produit/chauffage-bois/poele-chauffage-bois/', 301],
            '/etiquette-produit/saint-malo/page/2/' => ['/poele-a-bois-granules-saint-malo/', 301],
            '/etiquette-produit/bois/page/2/' => ['/categorie-produit/chauffage-bois/', 301],
            '/author/bubble/' => ['/', 301],
            '/author/admin/' => ['/', 301],
            '/2022/11/28/bonjour-tout-le-monde/' => ['/', 410],
        ];
        foreach ($map as $from => [$to, $code]) {
            DB::table('redirects')->updateOrInsert(['from_path' => $from], ['to_path' => $to, 'status_code' => $code, 'note' => 'Search Console — mise en ligne 04/11/2026', 'created_at' => $now, 'updated_at' => $now]);
        }
        DB::table('settings')->insertOrIgnore(['key' => 'seo.google_site_verification', 'value' => '', 'group' => 'seo', 'created_at' => $now, 'updated_at' => $now]);
        cache()->forget('settings.all');
    }

    public function down(): void
    {
    }
};
