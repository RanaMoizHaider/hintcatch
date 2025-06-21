<?php

namespace App\Models;

use App\Models\Scopes\ApprovedScope;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
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
        'logo',
        'features',
        'best_practices',
        'is_approved',
        'user_id',
    ];

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    protected $casts = [
        'features' => 'array',
        'best_practices' => 'array',
        'is_approved' => 'boolean',
    ];

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'platform_prompts');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to include only approved platforms
     * Note: Global scope already filters for approved, so this is mainly for explicit calls
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('is_approved', true);
    }

    /**
     * Scope to include only unapproved platforms
     * Removes global scope first to avoid conflicts
     */
    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class)
              ->where('is_approved', false);
    }

    /**
     * Scope to include unapproved platforms in query results
     */
    #[Scope]
    protected function withUnapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class);
    }
}
