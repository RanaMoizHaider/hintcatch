<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Config extends Model
{
    /** @use HasFactory<\Database\Factories\ConfigFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'config_type_id',
        'agent_id',
        'user_id',
        'category_id',
        'source_url',
        'source_author',
        'downloads',
        'vote_score',
        'version',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'downloads' => 'integer',
            'vote_score' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ConfigType, $this>
     */
    public function configType(): BelongsTo
    {
        return $this->belongsTo(ConfigType::class);
    }

    /**
     * @return BelongsTo<Agent, $this>
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ConfigFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(ConfigFile::class)->orderBy('order');
    }

    /**
     * Get the primary file for this config.
     *
     * @return HasMany<ConfigFile, $this>
     */
    public function primaryFile(): HasMany
    {
        return $this->hasMany(ConfigFile::class)->where('is_primary', true);
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
        return $this->morphMany(Favorite::class, 'favorable');
    }

    /**
     * Connected configs (variants, alternatives, etc.)
     *
     * @return BelongsToMany<Config, $this>
     */
    public function connectedConfigs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'config_connections', 'config_id', 'connected_config_id')
            ->withPivot('relationship_type')
            ->withTimestamps();
    }

    /**
     * Configs that connect to this one.
     *
     * @return BelongsToMany<Config, $this>
     */
    public function connectedFrom(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'config_connections', 'connected_config_id', 'config_id')
            ->withPivot('relationship_type')
            ->withTimestamps();
    }

    /**
     * All related configs (both directions).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Config>
     */
    public function allConnections(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->connectedConfigs->merge($this->connectedFrom);
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
