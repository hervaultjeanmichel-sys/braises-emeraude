<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Contenus lot 1 : catégories, marques, magasin de Dinard (database/data/content-lot1.php).
 * Ne remplit que les champs vides : rien de ce qui a été saisi dans l'admin n'est écrasé.
 *
 *   php artisan db:seed --class=ContentLot1Seeder
 */
class ContentLot1Seeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('data/content-lot1.php');

        $this->fill(ProductCategory::class, 'path', $data['categories']);
        $this->fill(Brand::class, 'slug', $data['brands']);
        $this->fill(Store::class, 'slug', $data['stores']);

        Cache::flush();
    }

    /** @param class-string<\Illuminate\Database\Eloquent\Model> $model */
    private function fill(string $model, string $key, array $rows): void
    {
        foreach ($rows as $value => $fields) {
            $record = $model::where($key, $value)->first();
            if (! $record) {
                $this->command?->warn("  ✗ {$value} : introuvable");
                continue;
            }
            $done = [];
            foreach ($fields as $field => $content) {
                if (blank($record->{$field})) {
                    $record->{$field} = $content;
                    $done[] = $field;
                }
            }
            $record->save();
            $this->command?->line('  ✓ '.$value.' : '.($done ? implode(', ', $done) : 'déjà rempli'));
        }
    }
}
