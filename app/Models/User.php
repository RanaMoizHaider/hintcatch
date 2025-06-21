<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = ['is_admin'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'bio',
        'avatar',
        'cover',
        'location',
        'website',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_admin' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * Get the user's avatar URL
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => 'https://unavatar.io/'.$this->email
        );
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('follower_id', $user->id)->exists();
    }

    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('user_id', $user->id)->exists();
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    /**
     * Scope to include only admin users
     */
    #[Scope]
    protected function admins(Builder $query): void
    {
        $query->where('is_admin', true);
    }

    /**
     * Scope to include only regular (non-admin) users
     */
    #[Scope]
    protected function regular(Builder $query): void
    {
        $query->where('is_admin', false);
    }

    /**
     * Scope to include users with verified email
     */
    #[Scope]
    protected function verified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to include users with unverified email
     */
    #[Scope]
    protected function unverified(Builder $query): void
    {
        $query->whereNull('email_verified_at');
    }
}
