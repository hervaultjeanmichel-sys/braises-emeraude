<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Demandes centralisées : toute demande (devis, contact, concours, rendez-vous)
 * devient une fiche en base, routée vers un magasin.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Listes du formulaire de devis, gérées dans l'admin
        Schema::create('form_options', function (Blueprint $t) {
            $t->id();
            $t->string('list', 40);                     // project_type | appliance_type | energy | replacement | budget | delay | floor
            $t->string('value', 60);
            $t->string('label');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['list', 'value']);
        });

        Schema::create('leads', function (Blueprint $t) {
            $t->id();
            $t->string('type', 20)->default('devis');   // devis | contact | concours | rdv
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $t->string('status', 20)->default('nouvelle'); // nouvelle | rappelee | visite | devis_envoye | signee | perdue
            $t->string('source', 40)->nullable();       // google | reseaux | campagne-rennes | direct
            $t->json('utm')->nullable();
            // Qualification (formulaire en étapes)
            $t->string('project_type', 60)->nullable();
            $t->string('appliance_type', 60)->nullable();
            $t->string('energy', 60)->nullable();
            $t->string('replacement', 60)->nullable();
            $t->unsignedSmallInteger('surface_m2')->nullable();
            $t->string('has_flue', 20)->nullable();
            $t->string('floor', 40)->nullable();
            $t->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $t->string('budget', 60)->nullable();
            $t->string('delay', 60)->nullable();
            $t->json('products')->nullable();           // liste « Mon projet » [{product_id, name, brand, note}]
            // Coordonnées
            $t->string('name');
            $t->string('phone', 30)->nullable();
            $t->string('email')->nullable();
            $t->string('postal_code', 10)->nullable();
            $t->text('message')->nullable();
            $t->json('photos')->nullable();
            $t->timestamp('consent_at')->nullable();    // RGPD
            $t->string('ip_hash', 64)->nullable();
            // IA Claude (préparé, validé par l'équipe)
            $t->text('ai_summary')->nullable();
            $t->string('ai_priority', 10)->nullable();  // haute | moyenne | basse
            $t->text('ai_missing')->nullable();
            $t->longText('ai_draft')->nullable();
            // OpenFire
            $t->string('openfire_id')->nullable();
            $t->timestamp('exported_at')->nullable();
            $t->timestamps();
            $t->index(['store_id', 'status']);
        });

        Schema::create('lead_notes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->text('body');
            $t->timestamps();
        });

        // Rattachement des comptes admin à un magasin (null = accès groupe)
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('store_id')->nullable()->after('email')->constrained()->nullOnDelete();
            $t->boolean('is_admin')->default(false)->after('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('store_id');
            $t->dropColumn('is_admin');
        });
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('form_options');
    }
};
