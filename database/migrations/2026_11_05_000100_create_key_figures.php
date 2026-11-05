<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Bandeau « Chiffres clés » avant le pied de page, sur toutes les pages (JM, 05/11/2026).
 * Chiffres validés par le client. Tout est modifiable dans Contenus › Chiffres clés. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('key_figures', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->nullable();          // ex. « Depuis », « Plus de »
            $table->unsignedInteger('value')->default(0);
            $table->string('suffix')->nullable();          // ex. « + », « % »
            $table->string('label');
            $table->string('source')->default('manual');   // manual | brands_count
            $table->boolean('animate')->default(true);     // compteur animé (désactiver pour une année)
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('key_figures')->insert([
            ['prefix' => 'Depuis', 'value' => 2013, 'suffix' => null, 'label' => 'à votre service', 'source' => 'manual', 'animate' => false, 'sort' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['prefix' => null, 'value' => 3, 'suffix' => null, 'label' => 'agences', 'source' => 'manual', 'animate' => true, 'sort' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['prefix' => null, 'value' => 20, 'suffix' => null, 'label' => 'personnes dans l\'équipe', 'source' => 'manual', 'animate' => true, 'sort' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['prefix' => 'Plus de', 'value' => 15, 'suffix' => null, 'label' => 'marques', 'source' => 'manual', 'animate' => true, 'sort' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $defaults = require database_path('data/settings-defaults.php');
        foreach ($defaults as $key => $row) {
            if (str_starts_with($key, 'figures.')) {
                DB::table('settings')->insertOrIgnore(['key' => $key, 'value' => $row['value'], 'group' => $row['group'], 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        cache()->forget('settings.all');
    }

    public function down(): void
    {
        Schema::dropIfExists('key_figures');
        DB::table('settings')->where('key', 'like', 'figures.%')->delete();
        cache()->forget('settings.all');
    }
};
