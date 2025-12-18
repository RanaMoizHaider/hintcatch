<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'config_type_id',
        'description',
    ];

    /**
     * @return BelongsTo<ConfigType, $this>
     */
    public function configType(): BelongsTo
    {
        return $this->belongsTo(ConfigType::class);
    }

    /**
     * @return HasMany<Config, $this>
     */
    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }
}
