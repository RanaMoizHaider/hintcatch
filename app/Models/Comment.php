<?php

namespace App\Models;

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
        static::addGlobalScope('visible', function (Builder $builder) {
            $builder->where('approved', true);
        });
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

    #[Scope]
    protected function unapproved(Builder $query): void
    {
        $query->where('approved', false);
    }
}
