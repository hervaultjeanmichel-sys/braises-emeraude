<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Le chemin complet suit le parent : chauffage-bois/poele-chauffage-bois
        static::saving(function (self $cat) {
            $cat->path = $cat->parent ? $cat->parent->path.'/'.$cat->slug : $cat->slug;
        });
    }

    public function url(): string
    {
        return '/categorie-produit/'.$this->path.'/';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** Ligne de marques de la carte « univers » : saisie dans l'admin, sinon calculée. */
    public function brandsLine(int $max = 5): string
    {
        if ($this->brands_line) {
            return $this->brands_line;
        }

        return Brand::published()
            ->whereHas('products', fn ($q) => $q->published()->whereIn('product_category_id', $this->selfAndChildrenIds()))
            ->orderBy('sort')->limit($max)->pluck('name')->implode(' · ');
    }

    /** Ids de la catégorie et de ses sous-catégories (2 niveaux). */
    public function selfAndChildrenIds(): array
    {
        return [$this->id, ...$this->children()->pluck('id')->all()];
    }
}
