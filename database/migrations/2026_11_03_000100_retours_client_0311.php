<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Retours client du 03/11/2026 :
 * - showroom zone Saint-Malo : Baguer-Morvan (et non Baguer-Pican)
 * - marques retirées : Turbo Fonte, Sergio Leoni, Stoveitaly (marque + produits hors ligne, 301) ; marque ajoutée : M Design
 * - page /chauffage/ : cartes choisies dans l'admin (on_heating_page) → bois, granulés, mixte + inserts, sans cuisson
 * - volets promo encore sur l'ancienne couleur bronze → #aba396
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Catégories : carte sur /chauffage/ + titre de carte
        if (! Schema::hasColumn('product_categories', 'on_heating_page')) {
            Schema::table('product_categories', function (Blueprint $t) {
                $t->string('card_title')->nullable()->after('icon');
                $t->boolean('on_heating_page')->default(false)->after('card_title');
            });
        }
        DB::table('product_categories')->whereIn('path', ['chauffage-bois', 'chauffage-granule', 'chauffage-mixte', 'chauffage-bois/insert-chauffage-bois', 'chauffage-granule/insert'])->update(['on_heating_page' => true]);
        DB::table('product_categories')->where('path', 'chauffage-bois/insert-chauffage-bois')->whereNull('card_title')->update(['card_title' => 'Inserts à bois']);
        DB::table('product_categories')->where('path', 'chauffage-granule/insert')->whereNull('card_title')->update(['card_title' => 'Inserts à granulés']);

        // 2. Baguer-Pican → Baguer-Morvan (magasins, pages, villes)
        foreach (['stores' => ['name', 'area_label', 'city', 'zone_label', 'zone_text', 'intro', 'content', 'seo_title', 'seo_description'], 'pages' => ['title', 'h1', 'intro', 'content', 'seo_title', 'seo_description']] as $table => $cols) {
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    DB::table($table)->where($col, 'like', '%Baguer-Pican%')->update([$col => DB::raw("REPLACE(`$col`, 'Baguer-Pican', 'Baguer-Morvan')")]);
                }
            }
        }

        // 3. Marques retirées : marque + produits hors ligne, anciennes URLs en 301
        $now = now();
        $gone = DB::table('brands')->whereIn('slug', ['turbo-fonte', 'sergio-leoni', 'stoveitaly'])->get(['id', 'slug']);
        foreach ($gone as $brand) {
            DB::table('redirects')->updateOrInsert(['from_path' => '/marque/'.$brand->slug.'/'], ['to_path' => '/chauffage/', 'status_code' => 301, 'note' => 'Marque retirée le 03/11/2026', 'updated_at' => $now, 'created_at' => $now]);
            $products = DB::table('products')->leftJoin('product_categories as c', 'c.id', '=', 'products.product_category_id')
                ->where('products.brand_id', $brand->id)->get(['products.id', 'products.slug', 'c.path']);
            foreach ($products as $p) {
                DB::table('redirects')->updateOrInsert(['from_path' => '/produit/'.$p->slug.'/'], ['to_path' => $p->path ? '/categorie-produit/'.$p->path.'/' : '/chauffage/', 'status_code' => 301, 'note' => 'Produit retiré (marque '.$brand->slug.') le 03/11/2026', 'updated_at' => $now, 'created_at' => $now]);
            }
            DB::table('products')->where('brand_id', $brand->id)->update(['is_published' => false, 'updated_at' => $now]);
            DB::table('brands')->where('id', $brand->id)->update(['is_published' => false, 'updated_at' => $now]);
        }
        DB::table('realisations')->whereIn('product_id', DB::table('products')->whereIn('brand_id', $gone->pluck('id'))->pluck('id'))->update(['product_id' => null]);

        // Marque ajoutée (textes, logo et magasins à compléter dans l'admin)
        if (! DB::table('brands')->where('slug', 'm-design')->exists()) {
            $id = DB::table('brands')->insertGetId(['name' => 'M Design', 'slug' => 'm-design', 'h1' => 'M Design', 'seo_title' => 'M Design : poêles et foyers', 'robots' => 'index,follow', 'sort' => (int) DB::table('brands')->max('sort') + 1, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now]);
            foreach (DB::table('stores')->pluck('id') as $storeId) {
                DB::table('brand_store')->insertOrIgnore(['brand_id' => $id, 'store_id' => $storeId]);
            }
        }

        // 4. Volets promo restés sur l'ancienne couleur
        if (Schema::hasTable('promo_slides')) {
            DB::table('promo_slides')->where('bg_color', '#8a5a16')->update(['bg_color' => '#aba396']);
        }

        cache()->forget('settings.all');
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_categories', 'on_heating_page')) {
            Schema::table('product_categories', fn (Blueprint $t) => $t->dropColumn(['card_title', 'on_heating_page']));
        }
    }
};
