<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'help_category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'steps',
        'related_links',
        'is_published',
        'last_reviewed_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'steps' => 'array',
            'related_links' => 'array',
            'is_published' => 'boolean',
            'last_reviewed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
