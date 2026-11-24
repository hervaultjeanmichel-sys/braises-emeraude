<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Code promo (JM, 24/11/2026) : code affiché sur le volet promo (ex. SISKO)
 * + champ « Code promo » dans les formulaires devis, contact et rendez-vous.
 * Textes dans Réglages › Textes du site (groupe « promo »). */
return new class extends Migration
{
    private const SETTINGS = [
        'form.promo_code' => 'Code promo',
        'form.promo_code_hint' => 'Facultatif : saisissez le code d\'une offre en cours.',
        'promo.code_label' => 'Code promo',
        'promo.code_copy' => 'Copier',
        'promo.code_copied' => 'Copié !',
    ];

    public function up(): void
    {
        foreach (['promo_slides', 'leads', 'appointments'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'promo_code')) {
                Schema::table($table, fn (Blueprint $t) => $t->string('promo_code', 40)->nullable());
            }
        }

        $now = now();
        foreach (self::SETTINGS as $key => $value) {
            DB::table('settings')->insertOrIgnore(['key' => $key, 'value' => $value, 'group' => 'promo', 'created_at' => $now, 'updated_at' => $now]);
        }

        // Volet affiché aujourd'hui sur l'accueil → code SISKO (s'il n'a pas déjà un code)
        $slide = \App\Models\PromoSlide::current(true);
        if ($slide && ! $slide->promo_code) {
            DB::table('promo_slides')->where('id', $slide->id)->update(['promo_code' => 'SISKO', 'updated_at' => $now]);
        }

        cache()->forget('settings.all');
    }

    public function down(): void
    {
        foreach (['promo_slides', 'leads', 'appointments'] as $table) {
            if (Schema::hasColumn($table, 'promo_code')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('promo_code'));
            }
        }
        DB::table('settings')->whereIn('key', array_keys(self::SETTINGS))->delete();
        cache()->forget('settings.all');
    }
};
