<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'api_endpoint',
        'logo',
        'color',
        'supported_features',
        'pricing_model',
        'is_active',
    ];

    protected $casts = [
        'supported_features' => 'array',
        'pricing_model' => 'array',
        'is_active' => 'boolean',
    ];

    public function aiModels(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }
}
