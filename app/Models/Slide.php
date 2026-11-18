<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slide extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'slider_id',
        'name',
        'headline',
        'subtitle',
        'alt_text',
        'cta_label',
        'cta_url',
        'sort_order',
        'is_enabled',
        'starts_at',
        'ends_at',
        'theme',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('desktop_image')->singleFile();
        $this->addMediaCollection('mobile_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(960)->nonQueued();
    }

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_enabled', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function imageUrl(): ?string
    {
        $media = $this->getFirstMedia('desktop_image') ?? $this->getFirstMedia('mobile_image');

        return $media?->getUrl();
    }
}
