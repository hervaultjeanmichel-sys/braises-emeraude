<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Réalisations : chantiers en photos, avec l'avis du client (publié seulement avec son accord). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisations', function (Blueprint $t) {
            $t->id();
            $t->string('title');                                    // « Poêle à granulés MCZ Ego à Dinard »
            $t->string('city')->nullable();                         // commune du chantier
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->string('energy', 20)->nullable();                   // bois | granules | mixte | gaz
            $t->date('installed_on')->nullable();
            $t->json('photos')->nullable();                         // chemins WebP (disque public)
            $t->text('description')->nullable();
            // Avis client
            $t->text('review_text')->nullable();
            $t->string('review_author')->nullable();                // « Marie L. »
            $t->unsignedTinyInteger('review_rating')->nullable();   // 1 à 5
            $t->date('review_date')->nullable();
            $t->string('review_source', 30)->nullable();            // google | facebook | questionnaire | direct
            $t->boolean('review_consent')->default(false);          // accord écrit du client pour publier avis + photos
            // Publication
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_published')->default(false);
            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();
            $t->index(['is_published', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisations');
    }
};
