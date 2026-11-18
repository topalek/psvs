<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactChannel extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'label', 'value', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function link(): string
    {
        return match ($this->type) {
            'phone' => 'tel:'.preg_replace('/[^0-9+]/', '', $this->value),
            'email' => 'mailto:'.$this->value,
            default => $this->value,
        };
    }
}
