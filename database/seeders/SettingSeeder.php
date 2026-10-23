<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Textes du site (en-tête, menus, libellés, formulaire de devis…) — tous éditables dans Réglages › Textes du site.
 * La liste vient de database/data/settings-defaults.php ; une valeur déjà modifiée dans l'admin n'est jamais écrasée.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = require database_path('data/settings-defaults.php');

        // Valeurs propres au client, prioritaires sur les défauts des gabarits
        $defaults['group.legal_name'] = ['group' => 'general', 'value' => 'La Soleillade'];
        $defaults['group.slogan'] = ['group' => 'general', 'value' => 'Le feu, c\'est la vie !'];
        $defaults['seo.title_suffix'] = ['group' => 'seo', 'value' => ' · Braises Émeraude'];

        foreach ($defaults as $key => $row) {
            Setting::firstOrCreate(['key' => $key], ['value' => $row['value'], 'group' => $row['group']]);
        }
    }
}
