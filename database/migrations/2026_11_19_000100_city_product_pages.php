<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Pages « ville + type de produit » (JM, 19/11/2026) : ex. « Poêle à granulés à Dinard ».
 * Une page ville peut être rattachée à une catégorie de produits : elle affiche alors les appareils de cette catégorie.
 * Textes des nouveaux titres modifiables dans Réglages › Textes du site.
 * (Clés insérées ici et non dans settings-defaults.php, modifié sur le serveur par le module Recrutement.) */
return new class extends Migration
{
    private array $settings = [
        'city.products_eyebrow' => 'En showroom',
        'city.products_title' => 'Nos *:categorie* à :ville',
        'city.products_all' => 'Voir toute la gamme',
        'store.guides_title' => 'Nos pages *par appareil*',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('cities', 'product_category_id')) {
            Schema::table('cities', function (Blueprint $t) {
                $t->foreignId('product_category_id')->nullable()->after('store_id')->constrained()->nullOnDelete();
            });
        }
        $now = now();
        foreach ($this->settings as $key => $value) {
            DB::table('settings')->insertOrIgnore(['key' => $key, 'value' => $value, 'group' => 'general', 'created_at' => $now, 'updated_at' => $now]);
        }
        cache()->forget('settings.all');
    }

    public function down(): void
    {
        Schema::table('cities', function (Blueprint $t) {
            $t->dropConstrainedForeignId('product_category_id');
        });
        DB::table('settings')->whereIn('key', array_keys($this->settings))->delete();
        cache()->forget('settings.all');
    }
};
