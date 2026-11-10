<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* Chaque demande (devis, contact) est aussi notifiée à l'adresse générale (JM, 10/11/2026).
 * Modifiable dans Réglages › Textes du site (groupe « devis », clé leads.notify_email). */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        DB::table('settings')->insertOrIgnore(['key' => 'leads.notify_email', 'value' => 'contact@braisesemeraude.com', 'group' => 'devis', 'created_at' => $now, 'updated_at' => $now]);
        cache()->forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'leads.notify_email')->delete();
        cache()->forget('settings.all');
    }
};
