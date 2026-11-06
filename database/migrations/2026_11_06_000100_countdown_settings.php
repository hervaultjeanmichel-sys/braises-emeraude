<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* Décompte avant l'ouverture de Rennes au-dessus de l'en-tête (JM, 06/11/2026).
 * Textes et date modifiables dans Réglages › Textes du site (groupe « countdown »). */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $defaults = require database_path('data/settings-defaults.php');
        foreach ($defaults as $key => $row) {
            if (str_starts_with($key, 'countdown.')) {
                DB::table('settings')->insertOrIgnore(['key' => $key, 'value' => $row['value'], 'group' => $row['group'], 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        cache()->forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'like', 'countdown.%')->delete();
        cache()->forget('settings.all');
    }
};
