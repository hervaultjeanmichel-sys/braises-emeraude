<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'power_kw' => 'float',
        ];
    }

    public function url(): string
    {
        return '/produit/'.$this->slug.'/';
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function mainImage(): ?string
    {
        return $this->images[0] ?? null;
    }
}
