<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'logo',
        'features',
        'best_practices',
        'is_approved',
        'user_id',
    ];

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    protected $casts = [
        'features' => 'array',
        'best_practices' => 'array',
        'is_approved' => 'boolean',
    ];

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'platform_prompts')->published()->visible();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
