<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConfigType extends Model
{
    /** @use HasFactory<\Database\Factories\ConfigTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'allowed_formats',
        'allows_multiple_files',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'allowed_formats' => 'array',
            'allows_multiple_files' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Config, $this>
     */
    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
