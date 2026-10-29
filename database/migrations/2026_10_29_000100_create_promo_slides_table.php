<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_slides', function (Blueprint $t) {
            $t->id();
            $t->string('name');                         // nom interne (admin)
            $t->string('eyebrow')->nullable();          // sur-titre
            $t->string('title')->nullable();
            $t->text('text')->nullable();
            $t->string('image')->nullable();
            $t->string('image_alt')->nullable();
            $t->string('bg_color', 9)->default('#34e3d3');
            $t->string('cta_label')->nullable();
            $t->string('cta_url')->nullable();
            $t->dateTime('starts_at')->nullable();
            $t->dateTime('ends_at')->nullable();
            $t->string('scope', 10)->default('all');    // all | home
            $t->unsignedSmallInteger('delay')->default(3); // secondes avant l'apparition
            $t->string('frequency', 10)->default('day');  // session | day | week | always
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_slides');
    }
};
