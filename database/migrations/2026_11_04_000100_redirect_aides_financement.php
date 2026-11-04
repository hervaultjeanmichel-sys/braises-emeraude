<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/* /aides-financement/ mise hors ligne par JM (04/11/2026) : 301 vers /chauffage/ pour ne pas perdre le référencement.
 * Si la page est remise en ligne, elle reprend la main automatiquement (middleware LegacyUrls). */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('redirects')->updateOrInsert(
            ['from_path' => '/aides-financement/'],
            ['to_path' => '/chauffage/', 'status_code' => 301, 'note' => 'Page hors ligne (plus d\'aides) — 04/11/2026', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('redirects')->where('from_path', '/aides-financement/')->delete();
    }
};
