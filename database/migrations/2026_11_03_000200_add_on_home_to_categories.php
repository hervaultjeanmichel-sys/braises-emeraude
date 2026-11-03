<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Accueil (03/11/2026) : cartes univers choisies dans l'admin — catégories principales + inserts bois et granulés */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_categories', 'on_home')) {
            Schema::table('product_categories', fn (Blueprint $t) => $t->boolean('on_home')->default(false)->after('on_heating_page'));
        }
        DB::table('product_categories')->whereNull('parent_id')->update(['on_home' => true]);
        DB::table('product_categories')->whereIn('path', ['chauffage-bois/insert-chauffage-bois', 'chauffage-granule/insert'])->update(['on_home' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_categories', 'on_home')) {
            Schema::table('product_categories', fn (Blueprint $t) => $t->dropColumn('on_home'));
        }
    }
};
