<?php

namespace App\Models;

use App\Models\Scopes\ApprovedScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to include only approved comments
     * Note: Global scope already filters for approved, so this is mainly for explicit calls
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        // Global scope already applies is_approved = true, so this is mainly for clarity
        $query->where('is_approved', true);
    }

    /**
     * Scope to include only unapproved comments
     * Removes global scope first to avoid conflicts
     */
    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class)
              ->where('is_approved', false);
    }

    /**
     * Scope to include unapproved comments (removes ApprovedScope).
     */
    #[Scope]
    protected function withUnapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class);
    }
}
