<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConfigType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'allowed_formats',
        'allows_multiple_files',
        'is_standard',
        'requires_agent',
    ];

    protected function casts(): array
    {
        return [
            'allowed_formats' => 'array',
            'allows_multiple_files' => 'boolean',
            'is_standard' => 'boolean',
            'requires_agent' => 'boolean',
        ];
    }

    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function isStandard(): bool
    {
        return $this->is_standard ?? false;
    }

    public function requiresAgent(): bool
    {
        return $this->requires_agent ?? false;
    }
}
