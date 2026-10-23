<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contenus éditables : pages CMS, articles, pages villes, pages aides,
 * réglages, bandeaux d'événement, redirections 301.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();               // « / » pour l'accueil, sinon chauffage-bois…
            $t->string('template', 40)->default('default'); // home | default | contact | realisations | legal
            $t->string('eyebrow')->nullable();
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();
            $t->json('sections')->nullable();           // blocs éditables (Builder Filament)
            $t->json('faq')->nullable();                // [{question, answer}] → FAQPage
            $t->string('hero_image')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->boolean('in_sitemap')->default(true);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug');
            $t->dateTime('published_at');               // URL /AAAA/MM/JJ/slug/
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $t->string('h1')->nullable();
            $t->text('excerpt')->nullable();
            $t->longText('content')->nullable();
            $t->string('image')->nullable();
            $t->json('faq')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->boolean('is_published')->default(true);
            $t->timestamps();
            $t->unique(['slug', 'published_at']);
        });

        Schema::create('cities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete(); // magasin le plus proche
            $t->string('name');
            $t->string('slug')->unique();               // poele-a-bois-granules-cancale
            $t->string('postal_code', 10)->nullable();
            $t->json('neighbours')->nullable();
            $t->string('eyebrow')->nullable();
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();        // 600 mots mini, propre à la ville
            $t->json('faq')->nullable();
            $t->string('hero_image')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('aid_topics', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();               // /aides/{slug}/
            $t->string('eyebrow')->nullable();
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();
            $t->json('amounts')->nullable();            // [{label, amount, condition}]
            $t->text('financing_example')->nullable();
            $t->date('verified_on')->nullable();        // « mis à jour le »
            $t->string('source_url')->nullable();       // France Rénov'
            $t->json('faq')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();                // convention O2Switch : colonne `key`
            $t->text('value')->nullable();
            $t->string('group', 40)->default('general');
            $t->timestamps();
        });

        Schema::create('event_banners', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->string('text')->nullable();
            $t->string('cta_label')->nullable();
            $t->string('cta_url')->nullable();
            $t->dateTime('starts_at')->nullable();
            $t->dateTime('ends_at')->nullable();
            $t->boolean('is_active')->default(false);
            $t->timestamps();
        });

        Schema::create('redirects', function (Blueprint $t) {
            $t->id();
            $t->string('from_path')->unique();          // /etiquette-produit/bois/
            $t->string('to_path');
            $t->unsignedSmallInteger('status_code')->default(301);
            $t->unsignedInteger('hits')->default(0);
            $t->timestamp('last_hit_at')->nullable();
            $t->string('note')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('event_banners');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('aid_topics');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('pages');
    }
};
