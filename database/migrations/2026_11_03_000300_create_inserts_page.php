<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * « Insert doit être présent partout » (JM, 03/11/2026) :
 * - pages.category_ids : catégories choisies pour une page Univers
 * - page /inserts/ (inserts bois + granulés), textes modifiables dans l'admin
 * - lien « Inserts » ajouté au menu principal (après Chauffage)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'category_ids')) {
            Schema::table('pages', fn (Blueprint $t) => $t->json('category_ids')->nullable()->after('template'));
        }

        $ids = DB::table('product_categories')->whereIn('path', ['chauffage-bois/insert-chauffage-bois', 'chauffage-granule/insert'])->orderBy('path')->pluck('id')->all();
        $now = now();

        if (! DB::table('pages')->where('slug', 'inserts')->exists()) {
            DB::table('pages')->insert([
                'title' => 'Inserts',
                'slug' => 'inserts',
                'template' => 'univers',
                'category_ids' => json_encode($ids),
                'eyebrow' => 'Inserts bois et granulés',
                'h1' => 'Inserts à bois et à granulés',
                'intro' => 'Votre cheminée ouverte peut devenir un vrai chauffage. Un insert se glisse dans le foyer existant : bûches ou granulés, nos équipes de Dinard, Saint-Malo et Rennes vous conseillent et assurent la pose.',
                'content' => <<<'HTML'
<h2>Pourquoi installer un insert dans votre cheminée ?</h2>
<p>Une cheminée ouverte est agréable à regarder, mais l'essentiel de la chaleur part par le conduit. L'insert ferme le foyer avec une porte vitrée : la flamme reste visible, la combustion est maîtrisée et la chaleur reste dans la pièce. Vous consommez moins de combustible pour un confort bien supérieur, avec moins de fumée et d'escarbilles.</p>

<h2>Insert à bois ou insert à granulés ?</h2>
<h3>L'insert à bois</h3>
<p>Pour le plaisir de la vraie flambée et des bûches qui crépitent. Simple, sans électricité, il convient à ceux qui ont un accès facile au bois de chauffage et aiment entretenir le feu.</p>
<h3>L'insert à granulés</h3>
<p>Pour le confort au quotidien : il s'allume et se règle comme un chauffage, souvent avec un thermostat et une programmation. Le réservoir assure plusieurs heures d'autonomie ; il demande en revanche un branchement électrique.</p>

<h2>Une pose adaptée à votre cheminée</h2>
<p>Chaque cheminée est différente : dimensions du foyer, état du conduit, habillage existant. Avant tout devis, nous vérifions ces points avec vous, en magasin ou lors d'une visite technique à domicile, pour choisir un insert qui s'intègre proprement et chauffe à la bonne puissance. Le tubage du conduit et les raccordements font partie de l'étude.</p>

<h2>Voir les inserts en vrai</h2>
<p>Plusieurs modèles sont exposés dans nos showrooms de Dinard · Pleurtuit, Saint-Malo · Baguer-Morvan et Rennes. Venez comparer les finitions, la taille des vitres et le fonctionnement, ou demandez directement votre devis gratuit.</p>
HTML,
                'seo_title' => 'Insert à bois et à granulés : pose en Ille-et-Vilaine',
                'seo_description' => 'Inserts à bois et à granulés pour transformer votre cheminée en vrai chauffage : conseil, visite technique et pose par Braises Émeraude à Dinard, Saint-Malo et Rennes.',
                'robots' => 'index,follow',
                'in_sitemap' => true,
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Menu principal : « Inserts » après « Chauffage »
        $menu = DB::table('settings')->where('key', 'menu.main')->value('value');
        if ($menu !== null && ! str_contains($menu, '/inserts/')) {
            $lines = preg_split('/\R/', $menu);
            $out = [];
            $done = false;
            foreach ($lines as $line) {
                $out[] = $line;
                if (! $done && str_contains($line, '|/chauffage/')) {
                    $out[] = 'Inserts|/inserts/';
                    $done = true;
                }
            }
            if (! $done) {
                $out[] = 'Inserts|/inserts/';
            }
            DB::table('settings')->where('key', 'menu.main')->update(['value' => implode("\n", $out), 'updated_at' => $now]);
        }

        cache()->forget('settings.all');
    }

    public function down(): void
    {
        if (Schema::hasColumn('pages', 'category_ids')) {
            Schema::table('pages', fn (Blueprint $t) => $t->dropColumn('category_ids'));
        }
    }
};
