<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'release_date' => 'datetime:Y-m-d',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $casts = [
        'features' => 'array',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'ai_model_prompts');
    }
}
