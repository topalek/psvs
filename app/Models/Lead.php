<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Lead extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Transient context used when a status change is performed through
     * LeadManager. Filament updates set these implicitly via auth()->id().
     */
    public ?int $statusChangedBy = null;

    public ?string $statusChangeComment = null;

    protected static function booted(): void
    {
        static::updated(function (Lead $lead): void {
            if (! $lead->wasChanged('status')) {
                return;
            }

            $from = $lead->getOriginal('status');

            LeadStatusHistory::create([
                'lead_id' => $lead->id,
                'user_id' => $lead->statusChangedBy ?? auth()->id(),
                'from_status' => $from instanceof LeadStatus ? $from->value : $from,
                'to_status' => $lead->status->value,
                'comment' => $lead->statusChangeComment,
            ]);

            $lead->statusChangedBy = null;
            $lead->statusChangeComment = null;
        });
    }

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'area_id',
        'locality',
        'street',
        'house',
        'object_type',
        'tariff_id',
        'company_name',
        'message',
        'source_page',
        'utm',
        'consent_version',
        'consent_at',
        'status',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeadType::class,
            'status' => LeadStatus::class,
            'utm' => 'array',
            'consent_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'assigned_to'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LeadStatusHistory::class)->latest();
    }

    public function addressLine(): string
    {
        return collect([$this->locality, $this->street, $this->house])->filter()->implode(', ');
    }
}
