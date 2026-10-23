<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'faq' => 'array',
            'in_sitemap' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function url(): string
    {
        return $this->slug === '/' ? '/' : '/'.$this->slug.'/';
    }
}
