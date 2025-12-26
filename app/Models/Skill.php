<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'submitted_by',
        'category_id',
        'license',
        'compatibility',
        'metadata',
        'allowed_tools',
        'scripts',
        'references',
        'assets',
        'source_url',
        'source_author',
        'github_url',

        'vote_score',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'compatibility' => 'array',
            'metadata' => 'array',
            'allowed_tools' => 'array',
            'scripts' => 'array',
            'references' => 'array',
            'assets' => 'array',

            'vote_score' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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

    public function updateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }

    public function generateSkillMd(): string
    {
        $frontmatter = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->license) {
            $frontmatter['license'] = $this->license;
        }

        if ($this->compatibility) {
            $frontmatter['compatibility'] = $this->compatibility;
        }

        if ($this->metadata) {
            $frontmatter['metadata'] = $this->metadata;
        }

        if ($this->allowed_tools) {
            $frontmatter['allowed-tools'] = $this->allowed_tools;
        }

        $yaml = \Symfony\Component\Yaml\Yaml::dump($frontmatter, 4, 2);

        return "---\n{$yaml}---\n\n{$this->content}";
    }

    public function generateIntegrationForAgent(Agent $agent): array
    {
        $template = $agent->skills_config_template;

        if (! $template) {
            return [];
        }

        $globalPath = $template['global_path'] ?? null;
        $projectPath = $template['project_path'] ?? null;

        return [
            'skill_md' => $this->generateSkillMd(),
            'scripts' => $this->scripts,
            'references' => $this->references,
            'assets' => $this->assets,
            'folder_name' => $this->slug,
            'install_path' => $globalPath ? "{$globalPath}{$this->slug}" : null,
            'project_path' => $projectPath ? "{$projectPath}{$this->slug}" : null,
        ];
    }
}
