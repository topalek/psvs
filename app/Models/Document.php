<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'version',
        'effective_date',
        'is_published',
        'is_public',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_published' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function fileUrl(): ?string
    {
        $media = $this->getFirstMedia('file');

        return $media ? $media->getUrl() : null;
    }
}
