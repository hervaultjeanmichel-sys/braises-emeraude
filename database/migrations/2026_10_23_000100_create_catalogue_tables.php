<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catalogue : magasins, marques, catégories, produits.
 * Toutes les entités publiques portent slug + seo_title + seo_description + robots.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $t) {
            $t->id();
            $t->string('name');                         // « Braises Émeraude Rennes »
            $t->string('short_name');                   // « Rennes »
            $t->string('area_label')->nullable();       // « Dinard · Pleurtuit »
            $t->string('slug')->unique();               // poele-a-bois-granules-rennes (URL conservée)
            $t->string('address')->nullable();
            $t->string('postal_code', 10)->nullable();
            $t->string('city')->nullable();
            $t->string('phone', 30)->nullable();
            $t->string('email')->nullable();            // reçoit les demandes du magasin
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->json('opening_hours')->nullable();      // [{day:1, opens:"09:00", closes:"12:00"}, …]
            $t->json('closed_dates')->nullable();
            $t->boolean('is_new')->default(false);      // badge « Nouveau »
            $t->string('badge_label')->nullable();
            $t->string('eyebrow')->nullable();
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();        // texte local riche (H2/H3)
            $t->json('served_communes')->nullable();    // communes desservies (affichage)
            $t->json('served_postcodes')->nullable();   // routage des demandes
            $t->json('photos')->nullable();
            $t->json('team')->nullable();
            $t->string('google_business_url')->nullable();
            // Prise de rendez-vous (étape Connexions)
            $t->boolean('booking_enabled')->default(false);
            $t->string('google_calendar_id')->nullable();
            $t->json('booking_settings')->nullable();
            // SEO
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('brands', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();               // /marque/{slug}/
            $t->string('logo')->nullable();
            $t->string('country')->nullable();
            $t->string('website')->nullable();
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('brand_store', function (Blueprint $t) {
            $t->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->primary(['brand_id', 'store_id']);   // marques exposées par magasin
        });

        Schema::create('product_categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $t->string('name');
            $t->string('slug');                          // segment : poele-chauffage-bois
            $t->string('path')->unique();                // chemin complet : chauffage-bois/poele-chauffage-bois
            $t->string('h1')->nullable();
            $t->text('intro')->nullable();
            $t->longText('content')->nullable();
            $t->string('image')->nullable();
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();               // /produit/{slug}/
            $t->string('energy', 20)->nullable();       // bois | granules | mixte | gaz
            $t->string('appliance_type', 20)->nullable(); // poele | insert | foyer | cuisiniere
            $t->decimal('power_kw', 5, 1)->nullable();
            $t->unsignedSmallInteger('surface_min_m2')->nullable();
            $t->unsignedSmallInteger('surface_max_m2')->nullable();
            $t->string('h1')->nullable();
            $t->text('excerpt')->nullable();
            $t->longText('content')->nullable();
            $t->json('specs')->nullable();              // [{label, value}]
            $t->json('images')->nullable();             // WebP
            $t->string('datasheet')->nullable();        // PDF
            $t->string('seo_title')->nullable();
            $t->string('seo_description', 320)->nullable();
            $t->string('robots')->default('index,follow');
            $t->boolean('is_featured')->default(false);
            $t->boolean('is_published')->default(true);
            $t->timestamps();
            $t->index(['energy', 'appliance_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('brand_store');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('stores');
    }
};
