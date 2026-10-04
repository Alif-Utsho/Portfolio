<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'type', 'title', 'slug', 'eyebrow', 'subtitle', 'summary', 'body', 'technologies',
    'metadata', 'url', 'secondary_url', 'image_path', 'is_published', 'is_featured', 'sort_order',
])]
class PortfolioItem extends Model
{
    public const TYPES = ['project', 'experience', 'skill', 'personal', 'social', 'navigation'];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'metadata' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
