<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Galerie FACTICE des showrooms (en attendant les vraies photos de chaque magasin).
 * Remplit seulement les magasins dont la galerie est vide, avec des copies (préfixe « factice- »)
 * de photos de réalisations, différentes d'un magasin à l'autre ; à défaut, des visuels « Photo à venir ».
 * Les photos se remplacent ensuite dans l'admin : Magasins › Galerie du showroom.
 *
 *   php artisan braises:store-gallery-placeholders            → simulation
 *   php artisan braises:store-gallery-placeholders --apply
 *   php artisan braises:store-gallery-placeholders --remove   → retire les photos factices
 */
class StoreGalleryPlaceholders extends Command
{
    protected $signature = 'braises:store-gallery-placeholders {--apply} {--remove} {--count=8 : photos par magasin}';

    protected $description = 'Galeries factices pour les pages showroom';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $stores = DB::table('stores')->orderBy('sort')->orderBy('id')->get(['id', 'short_name', 'photos']);

        if ($this->option('remove')) {
            foreach ($stores as $store) {
                $photos = json_decode((string) $store->photos, true) ?: [];
                $keep = array_values(array_filter($photos, fn ($p) => ! str_contains((string) $p, '/factice-')));
                if (count($keep) !== count($photos)) {
                    DB::table('stores')->where('id', $store->id)->update(['photos' => $keep ? json_encode($keep) : null]);
                    foreach (array_diff($photos, $keep) as $p) {
                        $disk->delete($p);
                    }
                    $this->line("{$store->short_name} : photos factices retirées");
                }
            }

            return self::SUCCESS;
        }

        // Réserve de photos : réalisations publiées (chemins du disque public), sinon visuels du dépôt
        $pool = DB::table('realisations')->where('is_published', true)->orderByDesc('is_featured')->orderBy('sort')->pluck('photos')
            ->flatMap(fn ($json) => json_decode((string) $json, true) ?: [])
            ->map(fn ($p) => preg_replace('#^/?storage/#', '', (string) $p))
            ->filter(fn ($p) => $p !== '' && $disk->exists($p))
            ->unique()->values();
        $fallback = collect(glob(database_path('data/factices/*.webp')) ?: [])->sort()->values();
        $this->info('Réserve : '.$pool->count().' photos de réalisations, '.$fallback->count().' visuels « Photo à venir »');

        $count = max(1, (int) $this->option('count'));
        $offset = 0;
        foreach ($stores as $store) {
            if (! empty(json_decode((string) $store->photos, true))) {
                $this->line("{$store->short_name} : galerie déjà remplie, ignorée");

                continue;
            }
            $new = [];
            for ($i = 0; $i < $count; $i++) {
                $target = 'magasins/factice-'.$store->id.'-'.($i + 1).'.webp';
                if ($pool->count() >= $count) {
                    $src = $pool[($offset + $i) % $pool->count()];
                    $this->option('apply') && $disk->put($target, (string) $disk->get($src));
                } elseif ($fallback->isNotEmpty()) {
                    $src = $fallback[($offset + $i) % $fallback->count()];
                    $this->option('apply') && $disk->put($target, (string) file_get_contents($src));
                } else {
                    break;
                }
                $new[] = $target;
            }
            $offset += $count;
            $this->line("{$store->short_name} : ".count($new).' photos factices'.($this->option('apply') ? ' ajoutées' : ' (simulation)'));
            if ($this->option('apply') && $new) {
                DB::table('stores')->where('id', $store->id)->update(['photos' => json_encode($new), 'updated_at' => now()]);
            }
        }

        if (! $this->option('apply')) {
            $this->comment('Simulation : relancer avec --apply');
        }

        return self::SUCCESS;
    }
}
