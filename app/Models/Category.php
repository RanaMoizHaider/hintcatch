<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, HasSlug;

    protected $guarded = [];

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
        return $this->hasMany(Prompt::class)->published()->visible();
    }

    public function countPrompts(): int
    {
        return $this->prompts()->count();
    }
}
