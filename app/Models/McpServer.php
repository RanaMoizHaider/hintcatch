<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class McpServer extends Model
{
    /** @use HasFactory<\Database\Factories\McpServerFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'url',
        'command',
        'args',
        'env',
        'headers',
        'submitted_by',
        'source_url',
        'source_author',
        'github_url',
        'vote_score',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'args' => 'array',
            'env' => 'array',
            'headers' => 'array',
            'vote_score' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    /**
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return MorphMany<Favorite, $this>
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Recalculate and update the vote score.
     */
    public function updateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }

    /**
     * Check if this is a remote MCP server.
     */
    public function isRemote(): bool
    {
        return $this->type === 'remote';
    }

    /**
     * Check if this is a local MCP server.
     */
    public function isLocal(): bool
    {
        return $this->type === 'local';
    }

    /**
     * Generate comprehensive agent-specific integration data for a given agent.
     *
     * @return array<string, mixed>
     */
    public function generateIntegrationForAgent(Agent $agent): array
    {
        $template = $agent->mcp_config_template;

        if (! $template || ! $agent->supports_mcp) {
            return [];
        }

        $serverConfig = $this->buildServerConfig($agent, $template);

        if (empty($serverConfig)) {
            return [];
        }

        $wrapperKey = $template['wrapper_key'] ?? 'mcpServers';
        $configPaths = $agent->mcp_config_paths ?? [];

        // Build the full JSON config
        $jsonConfig = [
            $wrapperKey => [
                $this->slug => $serverConfig,
            ],
        ];

        // Build CLI add command if agent supports it
        $cliCommand = $this->buildCliAddCommand($agent, $template);

        return [
            'json_config' => $jsonConfig,
            'config_paths' => $configPaths,
            'cli_command' => $cliCommand,
            'transport_type' => $this->determineTransportType($agent),
        ];
    }

    /**
     * Build CLI add command for agents that support it (e.g., Claude Code).
     */
    protected function buildCliAddCommand(Agent $agent, array $template): ?string
    {
        $cliTemplate = $template['cli_add_command'] ?? null;

        if (! $cliTemplate) {
            return null;
        }

        // Replace placeholders in CLI command template
        $command = $cliTemplate;

        // Replace {name} with MCP server slug
        $command = str_replace('{name}', $this->slug, $command);

        if ($this->isLocal()) {
            // For local: claude mcp add server-name -- command args...
            $fullCommand = $this->command;
            if ($this->args) {
                $fullCommand .= ' '.implode(' ', array_map(fn ($arg) => escapeshellarg($arg), $this->args));
            }
            $command = str_replace('{command}', $fullCommand, $command);
            $command = str_replace('{url}', '', $command);
        } else {
            // For remote: claude mcp add server-name url
            $command = str_replace('{url}', $this->url ?? '', $command);
            $command = str_replace('{command}', '', $command);
        }

        // Add environment variables if present
        if ($this->env && str_contains($command, '{env_flags}')) {
            $envFlags = collect($this->env)
                ->map(fn ($value, $key) => "-e {$key}={$value}")
                ->implode(' ');
            $command = str_replace('{env_flags}', $envFlags, $command);
        } else {
            $command = str_replace('{env_flags}', '', $command);
        }

        // Clean up multiple spaces
        $command = preg_replace('/\s+/', ' ', trim($command));

        return $command;
    }

    /**
     * Generate agent-specific configuration for a given agent.
     *
     * @deprecated Use generateIntegrationForAgent() instead
     *
     * @return array<string, mixed>
     */
    public function generateConfigForAgent(Agent $agent): array
    {
        $integration = $this->generateIntegrationForAgent($agent);

        return $integration['json_config'] ?? [];
    }

    /**
     * Build the server configuration based on agent template and MCP server type.
     *
     * @param  array<string, mixed>  $template
     * @return array<string, mixed>
     */
    protected function buildServerConfig(Agent $agent, array $template): array
    {
        // Determine transport type based on MCP server type (local/remote)
        $transportType = $this->determineTransportType($agent);

        if (! $transportType) {
            return [];
        }

        $transportConfig = $template[$transportType] ?? null;

        if (! $transportConfig || ! isset($transportConfig['fields'])) {
            return [];
        }

        $fields = $transportConfig['fields'];
        $config = [];

        // Add type field if template specifies it
        if (isset($transportConfig['type_value'])) {
            $typeField = $fields['type'] ?? 'type';
            $config[$typeField] = $transportConfig['type_value'];
        }

        if ($this->isLocal()) {
            $config = $this->buildLocalConfig($config, $fields, $template);
        } else {
            $config = $this->buildRemoteConfig($config, $fields, $transportType);
        }

        return $config;
    }

    /**
     * Determine the transport type for this MCP server based on agent support.
     */
    protected function determineTransportType(Agent $agent): ?string
    {
        $supportedTypes = $agent->mcp_transport_types ?? [];

        if ($this->isLocal()) {
            // For local servers, prefer 'stdio' or 'local' transport
            if (in_array('stdio', $supportedTypes, true)) {
                return 'stdio';
            }
            if (in_array('local', $supportedTypes, true)) {
                return 'local';
            }

            return null;
        }

        // For remote servers, prefer 'http' > 'sse' > 'remote'
        foreach (['http', 'sse', 'remote'] as $type) {
            if (in_array($type, $supportedTypes, true)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Build configuration for local MCP servers.
     *
     * @param  array<string, mixed>  $config
     * @param  array<string, string>  $fields
     * @param  array<string, mixed>  $template
     * @return array<string, mixed>
     */
    protected function buildLocalConfig(array $config, array $fields, array $template): array
    {
        // Handle command field - some agents use array format, others use string
        if ($this->command) {
            $commandField = $fields['command'] ?? 'command';

            // OpenCode uses array format for command
            if (($template['local']['type_value'] ?? null) === 'local') {
                // For OpenCode, command should be an array including args
                $fullCommand = [$this->command];
                if ($this->args) {
                    $fullCommand = array_merge($fullCommand, $this->args);
                }
                $config[$commandField] = $fullCommand;
            } else {
                // Most agents use string command with separate args
                $config[$commandField] = $this->command;

                if ($this->args && isset($fields['args'])) {
                    $config[$fields['args']] = $this->args;
                }
            }
        }

        // Handle environment variables
        if ($this->env) {
            // Check for 'environment' (OpenCode) vs 'env' (most others)
            $envField = $fields['environment'] ?? $fields['env'] ?? 'env';
            $config[$envField] = $this->env;
        }

        return $config;
    }

    /**
     * Build configuration for remote MCP servers.
     *
     * @param  array<string, mixed>  $config
     * @param  array<string, string>  $fields
     * @return array<string, mixed>
     */
    protected function buildRemoteConfig(array $config, array $fields, string $transportType): array
    {
        if ($this->url) {
            // Handle different URL field names (url, httpUrl, serverUrl)
            $urlField = $fields['httpUrl'] ?? $fields['serverUrl'] ?? $fields['url'] ?? 'url';
            $config[$urlField] = $this->url;
        }

        if ($this->headers) {
            $headersField = $fields['headers'] ?? 'headers';
            $config[$headersField] = $this->headers;
        }

        return $config;
    }

    /**
     * Generate a complete config file content for an agent (JSON encoded).
     */
    public function generateConfigFileContent(Agent $agent): string
    {
        $config = $this->generateConfigForAgent($agent);

        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
