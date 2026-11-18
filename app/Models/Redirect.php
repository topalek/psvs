<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_path',
        'destination_path',
        'status_code',
        'is_active',
        'hits',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'hits' => 'integer',
        ];
    }

    public function normalizedSource(): string
    {
        return '/'.ltrim($this->source_path, '/');
    }
}
