<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Provider extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderFactory> */
    use HasFactory, HasSlug;

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
        'is_approved',
        'user_id',
    ];

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    protected $casts = [
        'supported_features' => 'array',
        'pricing_model' => 'array',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function aiModels(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prompts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Prompt::class,
            AiModel::class,
            'provider_id',
            'ai_model_id'
        )->published()->visible();
    }
}
