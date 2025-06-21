<?php

namespace App\Models;

use App\Models\Scopes\ApprovedScope;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
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
        'provider_id',
        'image',
        'color',
        'icon',
        'features',
        'release_date',
        'is_approved',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date:Y-m-d',
            'features' => 'array',
            'is_approved' => 'boolean',
        ];
    }

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'ai_model_prompts');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to include only approved AI models
     * Note: Global scope already filters for approved, so this is mainly for explicit calls
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        // Global scope already applies is_approved = true, so this is mainly for clarity
        $query->where('is_approved', true);
    }

    /**
     * Scope to include only unapproved AI models
     * Removes global scope first to avoid conflicts
     */
    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class)
              ->where('is_approved', false);
    }

    /**
     * Scope to include unapproved AI models in query results
     */
    #[Scope]
    protected function withUnapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class);
    }
}
