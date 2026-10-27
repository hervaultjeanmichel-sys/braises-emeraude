<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Store;
use App\Support\GoogleReviews;
use Illuminate\Console\Command;

/**
 * Avis Google des magasins.
 *   php artisan braises:google-reviews                    → note, nombre d'avis et avis de chaque magasin
 *   php artisan braises:google-reviews rennes --find      → cherche l'identifiant Google du magasin (nom + adresse)
 *   php artisan braises:google-reviews rennes --set=ChIJ… → enregistre l'identifiant choisi
 */
class GoogleReviewsCommand extends Command
{
    protected $signature = 'braises:google-reviews {store? : slug, nom court ou id} {--find} {--set= : Place ID à enregistrer}';

    protected $description = 'Avis Google des magasins (Places API)';

    public function handle(): int
    {
        if (! GoogleReviews::enabled()) {
            $this->error('GOOGLE_PLACES_KEY manquant dans le .env');

            return self::FAILURE;
        }
        $q = (string) $this->argument('store');
        $stores = $q === '' ? Store::orderBy('sort')->get()
            : Store::query()->where('id', $q)->orWhere('short_name', $q)->orWhere('slug', 'like', '%'.$q.'%')->get();
        if ($stores->isEmpty()) {
            $this->error("Magasin « {$q} » introuvable");

            return self::FAILURE;
        }

        foreach ($stores as $store) {
            $this->newLine();
            $this->line('<options=bold>'.$store->name.'</>');
            if ($id = $this->option('set')) {
                $store->forceFill(['google_place_id' => $id])->save();
                GoogleReviews::forget($store);
                $this->info('  Identifiant enregistré : '.$id);
            }
            if ($this->option('find')) {
                $query = trim($store->name.' '.$store->addressLine());
                foreach (GoogleReviews::findPlace($query) as $i => $p) {
                    $this->line(sprintf('  %d. %s — %s', $i + 1, $p['name'], $p['address']));
                    $this->line('     <fg=cyan>'.$p['id'].'</>');
                }
                if (! $store->google_place_id && ($first = GoogleReviews::findPlace($query)[0] ?? null) && str_contains(mb_strtolower($first['name']), 'braises')) {
                    $store->forceFill(['google_place_id' => $first['id']])->save();
                    $this->info('  → 1er résultat enregistré (vérifiez le nom et l’adresse ci-dessus ; sinon --set=)');
                }
            }
            $store->refresh();
            if (! $store->google_place_id) {
                $this->warn('  Pas d’identifiant Google : lancez --find');

                continue;
            }
            $d = GoogleReviews::forStore($store, true);
            if (! $d) {
                $this->error('  Avis indisponibles (voir storage/logs/laravel.log)');

                continue;
            }
            $this->line(sprintf('  Note Google : <fg=green>%s/5</> sur %d avis — %d avis renvoyés par l’API', $d['rating'] !== null ? number_format($d['rating'], 1, ',', '') : '—', $d['count'], count($d['reviews'])));
            foreach ($d['reviews'] as $r) {
                $this->line(sprintf('   %s %s · %s · %s%s', str_repeat('★', $r['rating']), $r['author'], $r['when'], mb_strimwidth(str_replace("\n", ' ', $r['text']), 0, 70, '…'), $r['visible'] ? '' : '  <fg=yellow>[masqué]</>'));
            }
        }

        return self::SUCCESS;
    }
}
