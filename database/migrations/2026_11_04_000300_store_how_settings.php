<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* Pages magasins (JM, 04/11/2026) : pas de devis en magasin, le devis passe par la visite d'un technicien.
 * Textes du bloc « Showroom et devis » créés dans les réglages pour être modifiables dans l'admin. */
return new class extends Migration
{
    public function up(): void
    {
        $defaults = require database_path('data/settings-defaults.php');
        $now = now();
        foreach ($defaults as $key => $row) {
            if (str_starts_with($key, 'store.how_') || $key === 'store.cta_visit') {
                DB::table('settings')->insertOrIgnore(['key' => $key, 'value' => $row['value'], 'group' => $row['group'], 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        cache()->forget('settings.all');
    }

    public function down(): void
    {
    }
};
