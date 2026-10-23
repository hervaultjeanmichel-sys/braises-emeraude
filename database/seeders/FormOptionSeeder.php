<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FormOption;
use Illuminate\Database\Seeder;

/** Listes du formulaire de devis en 8 étapes (cahier des charges), modifiables dans l'admin. */
class FormOptionSeeder extends Seeder
{
    public function run(): void
    {
        $lists = [
            'project_type' => ['neuf' => 'Construction neuve', 'renovation' => 'Rénovation', 'remplacement' => 'Remplacement d\'un appareil existant'],
            'appliance_type' => ['poele' => 'Poêle', 'insert' => 'Insert', 'foyer' => 'Foyer', 'cuisiniere' => 'Cuisinière'],
            'energy' => ['bois' => 'Bois', 'granules' => 'Granulés', 'mixte' => 'Mixte', 'nsp' => 'Je ne sais pas encore'],
            'replacement' => ['bois-bois' => 'Bois → bois', 'bois-granules' => 'Bois → granulés', 'fioul-elec' => 'Fioul ou électrique → bois/granulés', 'autre' => 'Autre'],
            'floor' => ['rdc' => 'Rez-de-chaussée', 'etage' => 'À l\'étage', 'plusieurs' => 'Sur plusieurs niveaux'],
            'budget' => ['moins-3000' => 'Moins de 3 000 €', '3000-6000' => '3 000 à 6 000 €', '6000-10000' => '6 000 à 10 000 €', 'plus-10000' => 'Plus de 10 000 €', 'nsp' => 'Je ne sais pas'],
            'delay' => ['urgent' => 'Le plus vite possible', '3-mois' => 'Dans les 3 mois', '6-mois' => 'Dans les 6 mois', 'reflexion' => 'Je me renseigne'],
        ];
        foreach ($lists as $list => $options) {
            $i = 0;
            foreach ($options as $value => $label) {
                FormOption::firstOrCreate(['list' => $list, 'value' => $value], ['label' => $label, 'sort' => $i++]);
            }
        }
    }
}
