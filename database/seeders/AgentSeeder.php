<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            [
                'name' => 'OpenCode',
                'slug' => 'opencode',
                'description' => 'AI coding agent built for the terminal by SST',
                'website' => 'https://opencode.ai',
                'docs_url' => 'https://opencode.ai/docs',
                'github_url' => 'https://github.com/sst/opencode',
                'supported_config_types' => ['mcp-servers', 'rules', 'agents', 'plugins', 'custom-tools', 'hooks', 'slash-commands', 'prompts'],
                'supported_file_formats' => ['json', 'jsonc', 'md', 'ts', 'js'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['local', 'remote'],
                'mcp_config_paths' => [
                    'project' => 'opencode.json',
                    'global' => '~/.config/opencode/config.json',
                ],
                'mcp_config_template' => [
                    // OpenCode uses "mcp" as wrapper with type field
                    'wrapper_key' => 'mcp',
                    'config_format' => 'json',
                    'local' => [
                        'type_value' => 'local',
                        'fields' => [
                            'type' => 'type',
                            'command' => 'command', // Array format: ["npx", "-y", "package"]
                            'environment' => 'environment',
                            'enabled' => 'enabled',
                            'timeout' => 'timeout',
                        ],
                    ],
                    'remote' => [
                        'type_value' => 'remote',
                        'fields' => [
                            'type' => 'type',
                            'url' => 'url',
                            'headers' => 'headers',
                            'enabled' => 'enabled',
                            'oauth' => 'oauth',
                            'timeout' => 'timeout',
                        ],
                    ],
                    // Example structure for viewing
                    'example_local' => [
                        'mcp' => [
                            'server-name' => [
                                'type' => 'local',
                                'command' => ['npx', '-y', 'mcp-server'],
                                'environment' => ['API_KEY' => 'value'],
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'example_remote' => [
                        'mcp' => [
                            'server-name' => [
                                'type' => 'remote',
                                'url' => 'https://example.com/mcp',
                                'headers' => ['Authorization' => 'Bearer token'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => 'AGENTS.md',
            ],
            [
                'name' => 'Claude Code',
                'slug' => 'claude-code',
                'description' => "Anthropic's official CLI coding agent",
                'website' => 'https://code.claude.com',
                'docs_url' => 'https://code.claude.com/docs/en/mcp',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'agents', 'plugins', 'custom-tools', 'hooks', 'slash-commands', 'skills', 'prompts'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'http', 'sse'],
                'mcp_config_paths' => [
                    'project' => '.mcp.json',
                    'global' => '~/.claude.json',
                ],
                'mcp_config_template' => [
                    // Claude Code uses "mcpServers" as wrapper with type field
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    // CLI command template for adding MCP servers
                    'cli_add_command' => 'claude mcp add {name} {env_flags} -- {command}',
                    'cli_add_command_remote' => 'claude mcp add {name} {url}',
                    'stdio' => [
                        'type_value' => 'stdio',
                        'fields' => [
                            'type' => 'type',
                            'command' => 'command', // String format
                            'args' => 'args',
                            'env' => 'env',
                            'envFile' => 'envFile',
                        ],
                    ],
                    'http' => [
                        'type_value' => 'http',
                        'fields' => [
                            'type' => 'type',
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'sse' => [
                        'type_value' => 'sse',
                        'fields' => [
                            'type' => 'type',
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'type' => 'stdio',
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                                'env' => ['API_KEY' => 'value'],
                            ],
                        ],
                    ],
                    'example_http' => [
                        'mcpServers' => [
                            'server-name' => [
                                'type' => 'http',
                                'url' => 'https://example.com/mcp',
                            ],
                        ],
                    ],
                ],
                'rules_filename' => 'CLAUDE.md',
            ],
            [
                'name' => 'Gemini CLI',
                'slug' => 'gemini-cli',
                'description' => "Google's open-source Gemini agent for terminal",
                'website' => 'https://geminicli.com',
                'docs_url' => 'https://geminicli.com/docs/tools/mcp-server',
                'github_url' => 'https://github.com/google-gemini/gemini-cli',
                'supported_config_types' => ['mcp-servers', 'rules', 'slash-commands', 'prompts', 'extensions'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse', 'http'],
                'mcp_config_paths' => [
                    'global' => '~/.gemini/settings.json',
                    'project' => '.gemini/settings.json',
                ],
                'mcp_config_template' => [
                    // Gemini uses "mcpServers" in settings.json, transport determined by field
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                            'cwd' => 'cwd',
                            'timeout' => 'timeout',
                            'trust' => 'trust',
                            'includeTools' => 'includeTools',
                            'excludeTools' => 'excludeTools',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                            'timeout' => 'timeout',
                            'trust' => 'trust',
                        ],
                    ],
                    'http' => [
                        'fields' => [
                            'httpUrl' => 'httpUrl',
                            'headers' => 'headers',
                            'timeout' => 'timeout',
                            'trust' => 'trust',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'python',
                                'args' => ['-m', 'my_mcp_server'],
                                'env' => ['API_KEY' => '$MY_API_KEY'],
                                'timeout' => 30000,
                            ],
                        ],
                    ],
                    'example_http' => [
                        'mcpServers' => [
                            'server-name' => [
                                'httpUrl' => 'https://example.com/mcp',
                                'headers' => ['Authorization' => 'Bearer token'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => 'GEMINI.md',
            ],
            [
                'name' => 'Codex',
                'slug' => 'codex',
                'description' => "OpenAI's terminal-based coding agent",
                'website' => 'https://github.com/openai/codex',
                'docs_url' => 'https://github.com/openai/codex#readme',
                'github_url' => 'https://github.com/openai/codex',
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'yaml'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'global' => '~/.codex/config.json',
                    'project' => '.codex/config.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => null,
            ],
            [
                'name' => 'Aider',
                'slug' => 'aider',
                'description' => 'AI pair programming in your terminal',
                'website' => 'https://aider.chat',
                'docs_url' => 'https://aider.chat/docs',
                'github_url' => 'https://github.com/paul-gauthier/aider',
                'supported_config_types' => ['rules', 'prompts'],
                'supported_file_formats' => ['yaml', 'md'],
                'supports_mcp' => false,
                'mcp_transport_types' => null,
                'mcp_config_paths' => null,
                'mcp_config_template' => null,
                'rules_filename' => '.aider',
            ],
            [
                'name' => 'Cursor',
                'slug' => 'cursor',
                'description' => 'AI-first code editor with agent mode',
                'website' => 'https://cursor.sh',
                'docs_url' => 'https://docs.cursor.com/context/model-context-protocol',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse', 'http'],
                'mcp_config_paths' => [
                    'project' => '.cursor/mcp.json',
                    'global' => '~/.cursor/mcp.json',
                ],
                'mcp_config_template' => [
                    // Cursor uses "mcpServers", transport determined by fields present
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'type' => 'type', // Optional, defaults to stdio
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                            'envFile' => 'envFile',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'http' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                                'env' => ['API_KEY' => 'value'],
                            ],
                        ],
                    ],
                    'example_remote' => [
                        'mcpServers' => [
                            'server-name' => [
                                'url' => 'https://example.com/mcp',
                                'headers' => ['Authorization' => 'Bearer token'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => '.cursorrules',
            ],
            [
                'name' => 'Cline',
                'slug' => 'cline',
                'description' => 'Autonomous coding agent for VS Code',
                'website' => 'https://github.com/cline/cline',
                'docs_url' => 'https://github.com/cline/cline#readme',
                'github_url' => 'https://github.com/cline/cline',
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'global' => '~/.vscode/extensions/cline/mcp_settings.json',
                    'project' => '.vscode/cline_mcp_settings.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                            'disabled' => 'disabled',
                            'alwaysAllow' => 'alwaysAllow',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'apiKey' => 'apiKey',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                                'env' => ['API_KEY' => 'value'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => '.clinerules',
            ],
            [
                'name' => 'Kimi CLI',
                'slug' => 'kimi-cli',
                'description' => "Moonshot AI's CLI agent with shell integration",
                'website' => 'https://github.com/MoonshotAI/kimi-cli',
                'docs_url' => 'https://github.com/MoonshotAI/kimi-cli#readme',
                'github_url' => 'https://github.com/MoonshotAI/kimi-cli',
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'yaml'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'global' => '~/.kimi/config.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => null,
            ],
            [
                'name' => 'Kiro',
                'slug' => 'kiro',
                'description' => "AWS's spec-driven agentic IDE",
                'website' => 'https://kiro.dev',
                'docs_url' => 'https://kiro.dev/docs',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts', 'specs'],
                'supported_file_formats' => ['json', 'yaml', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'project' => '.kiro/settings.json',
                    'global' => '~/.kiro/settings.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => null,
            ],
            [
                'name' => 'Windsurf',
                'slug' => 'windsurf',
                'description' => "Codeium's AI-powered IDE with Cascade agent",
                'website' => 'https://codeium.com/windsurf',
                'docs_url' => 'https://docs.codeium.com/windsurf/mcp',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'global' => '~/.codeium/windsurf/mcp_config.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'mcpServers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'serverUrl' => 'serverUrl',
                        ],
                    ],
                    'example_stdio' => [
                        'mcpServers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => '.windsurfrules',
            ],
            [
                'name' => 'Zed',
                'slug' => 'zed',
                'description' => 'High-performance code editor with AI assistance',
                'website' => 'https://zed.dev',
                'docs_url' => 'https://zed.dev/docs/assistant/context-servers',
                'github_url' => 'https://github.com/zed-industries/zed',
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio'],
                'mcp_config_paths' => [
                    'global' => '~/.config/zed/settings.json',
                    'project' => '.zed/settings.json',
                ],
                'mcp_config_template' => [
                    // Zed uses "context_servers" as wrapper
                    'wrapper_key' => 'context_servers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                        'settings_wrapper' => 'settings', // Zed wraps in settings object
                    ],
                    'example_stdio' => [
                        'context_servers' => [
                            'server-name' => [
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                                'env' => ['API_KEY' => 'value'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => null,
            ],
            [
                'name' => 'GitHub Copilot',
                'slug' => 'github-copilot',
                'description' => "GitHub's AI pair programmer in VS Code",
                'website' => 'https://github.com/features/copilot',
                'docs_url' => 'https://docs.github.com/copilot',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'prompts'],
                'supported_file_formats' => ['json', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'sse'],
                'mcp_config_paths' => [
                    'project' => '.vscode/mcp.json',
                    'global' => '~/.vscode/mcp.json',
                ],
                'mcp_config_template' => [
                    'wrapper_key' => 'servers',
                    'config_format' => 'json',
                    'stdio' => [
                        'fields' => [
                            'type' => 'type',
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                        ],
                    ],
                    'sse' => [
                        'fields' => [
                            'type' => 'type',
                            'url' => 'url',
                            'headers' => 'headers',
                        ],
                    ],
                    'example_stdio' => [
                        'servers' => [
                            'server-name' => [
                                'type' => 'stdio',
                                'command' => 'npx',
                                'args' => ['-y', 'mcp-server'],
                            ],
                        ],
                    ],
                ],
                'rules_filename' => '.github/copilot-instructions.md',
            ],
        ];

        foreach ($agents as $agent) {
            Agent::updateOrCreate(
                ['slug' => $agent['slug']],
                $agent
            );
        }
    }
}
