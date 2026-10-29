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
            $t->string('zone_label')->nullable()->after('area_label');   // « Zone Saint-Malo »
            $t->string('zone_text', 400)->nullable()->after('zone_label'); // communes couvertes
        });

        // Zones données par JM le 29/10/2026 (remplit seulement les champs vides)
        $zones = [
            'poele-a-bois-granules-dinard' => ['Zone de Dinard', 'Rive gauche de la Rance, environ 30 km autour de Dinard.'],
            'poele-a-bois-granules-saint-malo' => ['Zone Saint-Malo', 'Tout Saint-Malo, Cancale, jusqu\'à Combourg.'],
            'poele-a-bois-granules-rennes' => ['Zone Rennes', 'De Combourg à Rennes, Vitré et Fougères, environ 40 km autour de Rennes.'],
        ];
        foreach ($zones as $slug => [$label, $text]) {
            DB::table('stores')->where('slug', $slug)->whereNull('zone_label')->update(['zone_label' => $label]);
            DB::table('stores')->where('slug', $slug)->whereNull('zone_text')->update(['zone_text' => $text]);
        }
        // Le showroom de la zone Saint-Malo est à Baguer-Pican (confirmé par JM le 29/10/2026)
        DB::table('stores')->where('slug', 'poele-a-bois-granules-saint-malo')->where('city', 'Baguer-Morvan')->update(['city' => 'Baguer-Pican']);
    }

    public function down(): void
    {
        Schema::table('stores', fn (Blueprint $t) => $t->dropColumn(['zone_label', 'zone_text']));
    }
};
