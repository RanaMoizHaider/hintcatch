<?php

namespace App\Models;

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
    use HasFactory, InteractsWithViews, HasTags, HasSlug;

    protected $guarded = [];

    // Slug configuration
    protected $slugSource = 'title';
    protected $slugColumn = 'slug';

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

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('status', 'published')
              ->whereNotNull('published_at')
              ->where('published_at', '<=', now());
    }

    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('visibility', 'public');
    }

    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', true);
    }
}
