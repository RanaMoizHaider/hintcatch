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
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'config_type_id',
        'agent_id',
        'submitted_by',
        'category_id',
        'source_url',
        'source_author',
        'github_url',
        'instructions',
        'vote_score',
        'version',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'vote_score' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function configType(): BelongsTo
    {
        return $this->belongsTo(ConfigType::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ConfigFile::class)->orderBy('order');
    }

    public function primaryFile(): HasMany
    {
        return $this->hasMany(ConfigFile::class)->where('is_primary', true);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function connectedConfigs(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'config_connections', 'config_id', 'connected_config_id')
            ->withPivot('relationship_type')
            ->withTimestamps();
    }

    public function connectedFrom(): BelongsToMany
    {
        return $this->belongsToMany(Config::class, 'config_connections', 'connected_config_id', 'config_id')
            ->withPivot('relationship_type')
            ->withTimestamps();
    }

    public function allConnections(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->connectedConfigs->merge($this->connectedFrom);
    }

    public function updateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }

    public function generateIntegrationForAgent(Agent $agent): ?array
    {
        $configType = $this->configType;
        if (! $configType) {
            return null;
        }

        $template = $agent->getConfigTypeTemplate($configType->slug);
        if (! $template) {
            return null;
        }

        return [
            'config_paths' => [
                'global' => $template['global_path'] ?? null,
                'project' => $template['project_path'] ?? null,
            ],
            'config_format' => $template['config_format'] ?? null,
            'install_command' => $template['install_command'] ?? null,
            'file_extension' => $template['file_extension'] ?? null,
        ];
    }
}
