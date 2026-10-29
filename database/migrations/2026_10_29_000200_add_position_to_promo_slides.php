<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promo_slides', function (Blueprint $t) {
            $t->string('position', 12)->default('center')->after('bg_color'); // center | bottom-left
        });
    }

    public function down(): void
    {
        Schema::table('promo_slides', function (Blueprint $t) {
            $t->dropColumn('position');
        });
    }
};
