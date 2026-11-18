<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Promotion extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'starts_at',
        'ends_at',
        'is_indefinite',
        'status',
        'discount_type',
        'discount_value',
        'terms',
        'cta_label',
        'cta_url',
        'seo_title',
        'seo_description',
        'canonical_url',
        'og_image',
        'sort_order',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'discount_type' => DiscountType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
            'is_indefinite' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(400)
            ->nonQueued();
    }

    public function tariffs(): BelongsToMany
    {
        return $this->belongsToMany(Tariff::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', ContentStatus::Published->value)
            ->where('published_at', '<=', $now)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(function (Builder $q) use ($now) {
                $q->where('is_indefinite', true)
                    ->orWhere(fn (Builder $inner) => $inner->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('ends_at')->where('ends_at', '<', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isActiveNow(): bool
    {
        if ($this->status !== ContentStatus::Published) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if (! $this->is_indefinite && $this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function coverUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('cover');

        return $media ? ($conversion ? $media->getUrl($conversion) : $media->getUrl()) : null;
    }
}
