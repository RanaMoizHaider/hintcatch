<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
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
        'rules_filename',
        'logo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'supported_config_types' => 'array',
            'supported_file_formats' => 'array',
            'supports_mcp' => 'boolean',
            'mcp_transport_types' => 'array',
            'mcp_config_paths' => 'array',
            'mcp_config_template' => 'array',
        ];
    }

    /**
     * @return HasMany<Config, $this>
     */
    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }
}
