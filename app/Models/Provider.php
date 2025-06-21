<?php

namespace App\Models;

use App\Models\Scopes\ApprovedScope;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Provider extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderFactory> */
    use HasFactory, HasSlug;

    /**
     * Boot the model and add global scope
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedScope);
    }

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
        );
    }

    /**
     * Scope to include only approved providers
     * Note: Global scope already filters for approved, so this is mainly for explicit calls
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        // Global scope already applies is_approved = true, so this is mainly for clarity
        $query->where('is_approved', true);
    }

    /**
     * Scope to include only unapproved providers
     * Removes global scope first to avoid conflicts
     */
    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class)
              ->where('is_approved', false);
    }

    /**
     * Scope to include unapproved providers in query results
     */
    #[Scope]
    protected function withUnapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class);
    }
}
