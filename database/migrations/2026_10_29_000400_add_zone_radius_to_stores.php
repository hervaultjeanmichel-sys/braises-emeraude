<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $t) {
            $t->unsignedSmallInteger('zone_radius_km')->nullable()->after('zone_text'); // cercle sur la carte d'accueil
        });
        // Rayons donnés par JM le 29/10/2026 (Saint-Malo : Baguer-Pican → Saint-Malo / Cancale / Combourg ≈ 25 km)
        foreach (['poele-a-bois-granules-dinard' => 30, 'poele-a-bois-granules-saint-malo' => 25, 'poele-a-bois-granules-rennes' => 40] as $slug => $km) {
            DB::table('stores')->where('slug', $slug)->whereNull('zone_radius_km')->update(['zone_radius_km' => $km]);
        }
    }

    public function down(): void
    {
        Schema::table('stores', fn (Blueprint $t) => $t->dropColumn('zone_radius_km'));
    }
};
