<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
    use HasFactory;

    protected $guarded = [];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'features' => 'array',
        'best_practices' => 'array',
    ];

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'platform_prompts')->published()->visible();
    }
}
