<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormOption extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return array<string,string> value => label */
    public static function listFor(string $list): array
    {
        return static::where('list', $list)->where('is_active', true)->orderBy('sort')->pluck('label', 'value')->all();
    }
}
