<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\IncidentStatus;
use App\Enums\PostType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'type',
        'status',
        'published_at',
        'unpublish_at',
        'author_id',
        'seo_title',
        'seo_description',
        'canonical_url',
        'og_image',
        'starts_at',
        'ends_at',
        'incident_status',
        'operational_note',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'status' => ContentStatus::class,
            'incident_status' => IncidentStatus::class,
            'published_at' => 'datetime',
            'unpublish_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'post_area');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', ContentStatus::Published->value)
            ->where('published_at', '<=', $now)
            ->where(fn (Builder $q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>', $now));
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query->whereIn('type', [PostType::Maintenance->value, PostType::Outage->value]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isActiveIncident(): bool
    {
        return $this->type->isOperational()
            && $this->incident_status instanceof IncidentStatus
            && $this->incident_status->isActive();
    }

    public function coverUrl(string $conversion = ''): ?string
    {
        $media = $this->getFirstMedia('cover');

        return $media ? ($conversion ? $media->getUrl($conversion) : $media->getUrl()) : null;
    }
}
