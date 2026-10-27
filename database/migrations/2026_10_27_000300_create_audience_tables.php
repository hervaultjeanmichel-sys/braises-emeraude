<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mesure d'audience maison, sans cookie : un visiteur = empreinte hachée (IP + navigateur + jour + clé),
 * impossible à relier d'un jour à l'autre. Pages vues agrégées par jour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->date('day');
            $t->char('visitor', 64);                    // empreinte du jour (non réversible)
            $t->string('source', 30)->nullable();       // google | reseaux | direct | site-referent | campagne…
            $t->string('device', 10)->nullable();       // mobile | ordinateur
            $t->string('landing', 190)->nullable();     // page d'arrivée
            $t->unsignedInteger('pages')->default(1);
            $t->timestamp('last_seen_at');
            $t->unique(['day', 'visitor']);
            $t->index('last_seen_at');
        });
        Schema::create('page_views', function (Blueprint $t) {
            $t->date('day');
            $t->string('path', 190);
            $t->unsignedInteger('views')->default(0);
            $t->primary(['day', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('visits');
    }
};
