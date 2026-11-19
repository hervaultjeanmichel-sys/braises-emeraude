<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'neighbours' => 'array',
            'faq' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function url(): string
    {
        return '/'.$this->slug.'/';
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /** Page « ville + type de produit » (ex. poêle à granulés à Dinard) : catégorie affichée. */
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /** Nom du lieu dans les titres : la ville, ou celle du magasin pour une page « ville + produit ». */
    public function place(): string
    {
        return $this->product_category_id && $this->store ? $this->store->short_name : $this->name;
    }
}
