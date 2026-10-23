<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'faq' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /** Structure WordPress conservée : /AAAA/MM/JJ/slug/ */
    public function url(): string
    {
        return '/'.$this->published_at->format('Y/m/d').'/'.$this->slug.'/';
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
