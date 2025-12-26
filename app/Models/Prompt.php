<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Prompt extends Model
{
    /** @use HasFactory<\Database\Factories\PromptFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'category',
        'submitted_by',
        'source_url',
        'source_author',
        'github_url',

        'vote_score',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

            'vote_score' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return MorphMany<Favorite, $this>
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Recalculate and update the vote score.
     */
    public function updateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }
}
