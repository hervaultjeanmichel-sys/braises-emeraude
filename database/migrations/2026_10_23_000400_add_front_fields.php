<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Champs éditables demandés par le front Contrastes. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $t) {
            $t->string('region_label')->nullable()->after('area_label');  // « Côte d'Émeraude »
            $t->string('announcement')->nullable()->after('badge_label'); // « Inauguration en novembre 2026 »
            $t->string('card_text', 400)->nullable()->after('announcement'); // texte court de la carte d'accueil
        });

        Schema::table('product_categories', function (Blueprint $t) {
            $t->string('icon', 20)->nullable()->after('image');            // bois | granules | mixte | cuisson
            $t->string('card_text', 400)->nullable()->after('icon');
            $t->string('brands_line')->nullable()->after('card_text');     // vide = calculé depuis le catalogue
        });
    }

    public function down(): void
    {
        Schema::table('stores', fn (Blueprint $t) => $t->dropColumn(['region_label', 'announcement', 'card_text']));
        Schema::table('product_categories', fn (Blueprint $t) => $t->dropColumn(['icon', 'card_text', 'brands_line']));
    }
};
