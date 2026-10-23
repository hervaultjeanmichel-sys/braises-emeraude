<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Model;

class AidTopic extends Model
{
    use HasSeo;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amounts' => 'array',
            'faq' => 'array',
            'verified_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function url(): string
    {
        return '/aides/'.$this->slug.'/';
    }
}
