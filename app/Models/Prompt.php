<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use App\Models\Scopes\VisibilityScope;
use App\Traits\HasSlug;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Tags\HasTags;

class Prompt extends Model implements Viewable
{
    /** @use HasFactory<\Database\Factories\PromptFactory> */
    use HasFactory, HasSlug, HasTags, InteractsWithViews;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'user_id',
        'category_id',
        'visibility',
        'status',
        'featured',
        'published_at',
        'source',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Slug configuration
    protected $slugSource = 'title';

    protected $slugColumn = 'slug';

    /**
     * Boot the model and add global scopes
     */
    protected static function booted(): void
    {
        // Add global scopes for published and public visibility
        static::addGlobalScope(new PublishedScope);
        static::addGlobalScope(new VisibilityScope);

        // Handle published_at when status changes
        static::saving(function (Prompt $prompt) {
            if ($prompt->isDirty('status')) {
                if ($prompt->status === 'published' && $prompt->published_at === null) {
                    $prompt->published_at = now();
                } elseif ($prompt->status !== 'published') {
                    $prompt->published_at = null;
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function aiModels(): BelongsToMany
    {
        return $this->belongsToMany(AiModel::class, 'ai_model_prompts');
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'platform_prompts');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Scope a query to only include published prompts.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft prompts.
     */
    #[Scope]
    protected function draft(Builder $query): void
    {
        $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include public prompts.
     */
    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where('visibility', 'public');
    }

    /**
     * Scope a query to only include private prompts.
     */
    #[Scope]
    protected function private(Builder $query): void
    {
        $query->where('visibility', 'private');
    }

    /**
     * Scope a query to only include unlisted prompts.
     */
    #[Scope]
    protected function unlisted(Builder $query): void
    {
        $query->where('visibility', 'unlisted');
    }

    /**
     * Scope a query to only include featured prompts.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', true);
    }

    /**
     * Scope a query to include draft prompts (removes published global scope only).
     */
    #[Scope]
    protected function withDrafts(Builder $query): void
    {
        $query->withoutGlobalScope(PublishedScope::class);
    }

    /**
     * Scope a query to include private prompts (removes visibility global scope only).
     */
    #[Scope]
    protected function withPrivate(Builder $query): void
    {
        $query->withoutGlobalScope(VisibilityScope::class);
    }

    /**
     * Scope a query to include all prompts regardless of status or visibility.
     */
    #[Scope]
    protected function withAll(Builder $query): void
    {
        $query->withoutGlobalScopes([
            PublishedScope::class,
            VisibilityScope::class,
        ]);
    }
}
