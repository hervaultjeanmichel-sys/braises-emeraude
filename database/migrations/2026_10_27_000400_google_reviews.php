<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avis Google : on ne stocke que l'identifiant Google du magasin (autorisé sans limite par Google)
 * et nos choix d'affichage par avis (clé hachée). Le contenu des avis reste chez Google.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $t) {
            $t->string('google_place_id')->nullable()->after('google_business_url');
        });
        Schema::create('google_review_choices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->char('review_key', 40);                 // sha1 de l'identifiant Google de l'avis
            $t->boolean('is_visible')->default(true);
            $t->boolean('is_featured')->default(false);
            $t->timestamps();
            $t->unique(['store_id', 'review_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_review_choices');
        Schema::table('stores', fn (Blueprint $t) => $t->dropColumn('google_place_id'));
    }
};
