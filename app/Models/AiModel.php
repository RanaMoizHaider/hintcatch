<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiModel extends Model
{
    /** @use HasFactory<\Database\Factories\AiModelFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'provider_id',
        'image',
        'color',
        'icon',
        'features',
        'release_date',
        'is_approved',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date:Y-m-d',
            'features' => 'array',
            'is_approved' => 'boolean',
        ];
    }

    // Slug configuration
    protected $slugSource = 'name';

    protected $slugColumn = 'slug';

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function prompts(): BelongsToMany
    {
        return $this->belongsToMany(Prompt::class, 'ai_model_prompts')->published()->visible();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
