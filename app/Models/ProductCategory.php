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

    /** Ids de la catégorie et de ses sous-catégories (2 niveaux). */
    public function selfAndChildrenIds(): array
    {
        return [$this->id, ...$this->children()->pluck('id')->all()];
    }
}
