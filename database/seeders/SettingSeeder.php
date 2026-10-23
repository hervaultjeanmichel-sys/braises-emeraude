<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/** Textes globaux du site (en-tête, pied de page, libellés) — tous éditables dans Réglages › Textes du site. */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'general' => [
                'group.name' => 'Braises Émeraude',
                'group.legal_name' => 'La Soleillade',
                'group.slogan' => 'Le feu, c\'est la vie !',
                'group.logo' => '/img/logo.webp',
                'label.new' => 'Nouveau',
                'label.open' => 'Ouvert maintenant',
                'label.closed' => 'Fermé',
                'label.see_store' => 'Voir le magasin',
            ],
            'header' => [
                'nav.heating' => 'Chauffage', 'nav.cooking' => 'Cuisson', 'nav.aids' => 'Aides', 'nav.realisations' => 'Réalisations',
                'cta.quote' => 'Devis gratuit',
            ],
            'footer' => [
                'footer.legal' => '© Braises Émeraude · <a href="/mentions-legales/">Mentions légales</a> · <a href="/politique-de-confidentialite/">Confidentialité</a>',
            ],
            'seo' => [
                'seo.title_suffix' => ' · Braises Émeraude',
            ],
        ];
        foreach ($settings as $group => $items) {
            foreach ($items as $key => $value) {
                Setting::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
            }
        }
    }
}
