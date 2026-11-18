<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'technology_id',
        'name',
        'slug',
        'internal_code',
        'customer_type',
        'speed_mbps',
        'upload_speed_mbps',
        'regular_price',
        'promo_price',
        'price_period_days',
        'price_after_promo',
        'promo_starts_at',
        'promo_ends_at',
        'connection_fee',
        'equipment_fee',
        'equipment_info',
        'included_services',
        'terms',
        'badge',
        'is_featured',
        'is_active',
        'is_published',
        'effective_date',
        'last_verified_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'customer_type' => CustomerType::class,
            'included_services' => 'array',
            'promo_starts_at' => 'datetime',
            'promo_ends_at' => 'datetime',
            'effective_date' => 'date',
            'last_verified_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('is_active', true);
    }

    public function scopeForCustomerType(Builder $query, CustomerType|string $type): Builder
    {
        return $query->where('customer_type', $type instanceof CustomerType ? $type->value : $type);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPromoActive(): bool
    {
        if (! $this->promo_price) {
            return false;
        }

        $now = now();

        if ($this->promo_starts_at && $this->promo_starts_at->isFuture()) {
            return false;
        }

        if ($this->promo_ends_at && $this->promo_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function currentPrice(): int
    {
        return $this->isPromoActive() ? (int) $this->promo_price : (int) $this->regular_price;
    }

    public function formattedRegularPrice(): string
    {
        return self::formatMoney($this->regular_price);
    }

    public function formattedPromoPrice(): ?string
    {
        return $this->promo_price ? self::formatMoney($this->promo_price) : null;
    }

    public function formattedPriceAfterPromo(): ?string
    {
        return $this->price_after_promo ? self::formatMoney($this->price_after_promo) : null;
    }

    public static function formatMoney(?int $minor): string
    {
        if ($minor === null) {
            return '—';
        }

        return number_format($minor / 100, 0, ',', ' ').' ₽';
    }
}
