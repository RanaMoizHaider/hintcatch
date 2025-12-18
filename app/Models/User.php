<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'bio',
        'website',
        'github_id',
        'github_username',
        'github_token',
        'github_refresh_token',
        'gitlab_id',
        'gitlab_username',
        'gitlab_token',
        'gitlab_refresh_token',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'github_token',
        'github_refresh_token',
        'gitlab_token',
        'gitlab_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<Config, $this>
     */
    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }

    /**
     * @return HasMany<Prompt, $this>
     */
    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    /**
     * @return HasMany<McpServer, $this>
     */
    public function mcpServers(): HasMany
    {
        return $this->hasMany(McpServer::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany<Vote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Check if user has voted on a given item.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $votable
     */
    public function hasVotedOn($votable): bool
    {
        return $this->votes()
            ->where('votable_type', $votable->getMorphClass())
            ->where('votable_id', $votable->getKey())
            ->exists();
    }

    /**
     * Get user's vote on a given item.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $votable
     */
    public function getVoteOn($votable): ?Vote
    {
        return $this->votes()
            ->where('votable_type', $votable->getMorphClass())
            ->where('votable_id', $votable->getKey())
            ->first();
    }

    /**
     * Check if user has favorited a given item.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $favorable
     */
    public function hasFavorited($favorable): bool
    {
        return $this->favorites()
            ->where('favorable_type', $favorable->getMorphClass())
            ->where('favorable_id', $favorable->getKey())
            ->exists();
    }

    /**
     * Get the user's GitHub profile URL.
     *
     * @return Attribute<string|null, never>
     */
    protected function githubUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->github_username ? "https://github.com/{$this->github_username}" : null,
        );
    }

    /**
     * Get the user's profile URL.
     *
     * @return Attribute<string, never>
     */
    protected function profileUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => route('profile.show', $this->username),
        );
    }
}
