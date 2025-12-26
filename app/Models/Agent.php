<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'docs_url',
        'github_url',
        'supported_config_types',
        'supported_file_formats',
        'supports_mcp',
        'mcp_transport_types',
        'mcp_config_paths',
        'mcp_config_template',
        'skills_config_template',
        'config_type_templates',
        'rules_filename',
        'logo',
    ];

    protected function casts(): array
    {
        return [
            'supported_config_types' => 'array',
            'supported_file_formats' => 'array',
            'supports_mcp' => 'boolean',
            'mcp_transport_types' => 'array',
            'mcp_config_paths' => 'array',
            'mcp_config_template' => 'array',
            'skills_config_template' => 'array',
            'config_type_templates' => 'array',
        ];
    }

    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }

    public function getConfigTypeTemplate(string $configTypeSlug): ?array
    {
        return $this->config_type_templates[$configTypeSlug] ?? null;
    }

    public function supportsConfigType(string $configTypeSlug): bool
    {
        return in_array($configTypeSlug, $this->supported_config_types ?? []);
    }

    public function supportsSkills(): bool
    {
        return ! empty($this->skills_config_template);
    }
}
