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

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
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
        'parent_id',
        'is_approved',
        'user_id',
    ];

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    public function countPrompts(): int
    {
        return $this->prompts()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to include only approved categories
     * Note: Global scope already filters for approved, so this is mainly for explicit calls
     */
    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('is_approved', true);
    }

    /**
     * Scope to include only unapproved categories
     * Removes global scope first to avoid conflicts
     */
    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class)
              ->where('is_approved', false);
    }

    /**
     * Scope to include unapproved categories in query results
     */
    #[Scope]
    protected function withUnapproved(Builder $query): void
    {
        $query->withoutGlobalScope(ApprovedScope::class);
    }
}
