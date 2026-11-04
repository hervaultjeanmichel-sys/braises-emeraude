<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/* Retours client 04/11/2026 : logo de marque (ex. MCZ) sur un volet promo. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_slides', function (Blueprint $t) {
            if (! Schema::hasColumn('promo_slides', 'brand_id')) {
                $t->foreignId('brand_id')->nullable()->after('image_alt')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('promo_slides', 'logo')) {
                $t->string('logo')->nullable()->after('brand_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('promo_slides', function (Blueprint $t) {
            $t->dropConstrainedForeignId('brand_id');
            $t->dropColumn('logo');
        });
    }
};
