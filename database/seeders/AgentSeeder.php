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
                'supported_config_types' => ['mcp-servers', 'rules', 'agents', 'plugins', 'custom-tools', 'hooks', 'slash-commands', 'skills', 'prompts'],
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
                'logo' => 'https://opencode.ai/favicon.svg',
                'skills_config_template' => [
                    'config_format' => 'markdown',
                    'global_path' => '~/.config/opencode/skills/',
                    'project_path' => '.opencode/skills/',
                    'file_extension' => '.md',
                    'supports_subdirectories' => true,
                ],
                'config_type_templates' => [
                    'commands' => [
                        'global_path' => '~/.config/opencode/command/',
                        'project_path' => '.opencode/command/',
                        'config_format' => 'markdown',
                        'file_extension' => '.md',
                    ],
                    'plugins' => [
                        'global_path' => '~/.config/opencode/plugin/',
                        'project_path' => '.opencode/plugin/',
                        'config_format' => 'typescript',
                        'file_extension' => '.ts',
                        'npm_install' => [
                            'config_file' => 'opencode.json',
                            'config_key' => 'plugin',
                            'example' => '{"plugin": ["package-name"]}',
                        ],
                    ],
                    'hooks' => [
                        'global_path' => '~/.config/opencode/hooks/',
                        'project_path' => '.opencode/hooks/',
                        'config_format' => 'typescript',
                    ],
                    'agents' => [
                        'global_path' => '~/.config/opencode/agents/',
                        'project_path' => '.opencode/agents/',
                        'config_format' => 'yaml',
                    ],
                ],
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
                'logo' => 'https://www.google.com/s2/favicons?domain=claude.com&sz=128',
                'skills_config_template' => [
                    'config_format' => 'markdown',
                    'global_path' => '~/.claude/skills/',
                    'project_path' => '.claude/skills/',
                    'file_extension' => '.md',
                    'supports_subdirectories' => true,
                ],
                'config_type_templates' => [
                    'commands' => [
                        'global_path' => '~/.claude/commands/',
                        'project_path' => '.claude/commands/',
                        'config_format' => 'markdown',
                        'file_extension' => '.md',
                    ],
                    'hooks' => [
                        'global_path' => '~/.claude/hooks/',
                        'project_path' => '.claude/hooks/',
                        'config_format' => 'json',
                    ],
                    'plugins' => [
                        'install_method' => 'cli',
                        'install_command' => '/plugin install {name}@{marketplace}',
                        'marketplace_add_command' => '/plugin marketplace add {owner}/{repo}',
                        'scopes' => ['user', 'project', 'local'],
                        'plugin_structure' => [
                            'manifest' => '.claude-plugin/plugin.json',
                            'commands_dir' => 'commands/',
                            'agents_dir' => 'agents/',
                            'skills_dir' => 'skills/',
                            'hooks_file' => 'hooks/hooks.json',
                            'mcp_config' => '.mcp.json',
                            'lsp_config' => '.lsp.json',
                        ],
                        'test_command' => 'claude --plugin-dir ./my-plugin',
                    ],
                ],
            ],
            [
                'name' => 'Gemini CLI',
                'slug' => 'gemini-cli',
                'description' => "Google's open-source Gemini agent for terminal",
                'website' => 'https://geminicli.com',
                'docs_url' => 'https://geminicli.com/docs',
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
                'logo' => 'https://geminicli.com/icon.png',
                'skills_config_template' => null,
                'config_type_templates' => [
                    'extensions' => [
                        'global_path' => '~/.gemini/extensions/',
                        'config_format' => 'javascript',
                    ],
                ],
            ],
            [
                'name' => 'Codex',
                'slug' => 'codex',
                'description' => "OpenAI's terminal-based coding agent",
                'website' => 'https://openai.com/codex',
                'docs_url' => 'https://developers.openai.com/codex',
                'github_url' => 'https://github.com/openai/codex',
                'supported_config_types' => ['mcp-servers', 'rules', 'skills', 'prompts'],
                'supported_file_formats' => ['toml', 'md'],
                'supports_mcp' => true,
                'mcp_transport_types' => ['stdio', 'http'],
                'mcp_config_paths' => [
                    'global' => '~/.codex/config.toml',
                ],
                'mcp_config_template' => [
                    // Codex uses TOML format with [mcp_servers.<name>] tables
                    'wrapper_key' => 'mcp_servers',
                    'config_format' => 'toml',
                    // CLI command template for adding MCP servers
                    'cli_add_command' => 'codex mcp add {name} {env_flags} -- {command}',
                    'cli_add_command_remote' => 'codex mcp add {name} {url}',
                    'stdio' => [
                        'fields' => [
                            'command' => 'command',
                            'args' => 'args',
                            'env' => 'env',
                            'env_vars' => 'env_vars',
                            'cwd' => 'cwd',
                            'startup_timeout_sec' => 'startup_timeout_sec',
                            'tool_timeout_sec' => 'tool_timeout_sec',
                            'enabled' => 'enabled',
                            'enabled_tools' => 'enabled_tools',
                            'disabled_tools' => 'disabled_tools',
                        ],
                    ],
                    'http' => [
                        'fields' => [
                            'url' => 'url',
                            'bearer_token_env_var' => 'bearer_token_env_var',
                            'http_headers' => 'http_headers',
                            'env_http_headers' => 'env_http_headers',
                            'startup_timeout_sec' => 'startup_timeout_sec',
                            'tool_timeout_sec' => 'tool_timeout_sec',
                            'enabled' => 'enabled',
                            'enabled_tools' => 'enabled_tools',
                            'disabled_tools' => 'disabled_tools',
                        ],
                    ],
                    'example_stdio' => [
                        // TOML: [mcp_servers.context7]
                        'mcp_servers' => [
                            'context7' => [
                                'command' => 'npx',
                                'args' => ['-y', '@upstash/context7-mcp'],
                            ],
                        ],
                    ],
                    'example_http' => [
                        // TOML: [mcp_servers.figma]
                        'mcp_servers' => [
                            'figma' => [
                                'url' => 'https://mcp.figma.com/mcp',
                                'bearer_token_env_var' => 'FIGMA_OAUTH_TOKEN',
                            ],
                        ],
                    ],
                ],
                'rules_filename' => 'AGENTS.md',
                'logo' => 'https://www.google.com/s2/favicons?domain=openai.com&sz=128',
                'skills_config_template' => [
                    'config_format' => 'markdown',
                    'global_path' => '~/.codex/skills/',
                    'file_extension' => '.md',
                ],
                'config_type_templates' => null,
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
                'logo' => 'https://aider.chat/assets/logo.svg',
                'skills_config_template' => null,
                'config_type_templates' => null,
            ],
            [
                'name' => 'Cursor',
                'slug' => 'cursor',
                'description' => 'AI-first code editor with agent mode',
                'website' => 'https://cursor.sh',
                'docs_url' => 'https://docs.cursor.com/context/model-context-protocol',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'skills', 'prompts'],
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
                'logo' => 'https://www.google.com/s2/favicons?domain=cursor.sh&sz=128',
                'skills_config_template' => [
                    'config_format' => 'markdown',
                    'global_path' => '~/.cursor/skills/',
                    'project_path' => '.cursor/skills/',
                    'file_extension' => '.md',
                ],
                'config_type_templates' => null,
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
                'logo' => 'https://raw.githubusercontent.com/cline/cline/main/assets/icons/icon.png',
                'skills_config_template' => null,
                'config_type_templates' => null,
            ],
            [
                'name' => 'Kimi CLI',
                'slug' => 'kimi-cli',
                'description' => "Moonshot AI's CLI agent with shell integration",
                'website' => 'https://www.kimi.com/coding',
                'docs_url' => 'https://www.kimi.com/coding/docs/en/kimi-cli.html',
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
                'logo' => 'https://statics.moonshot.cn/moonshot-ai/favicon.ico',
                'skills_config_template' => null,
                'config_type_templates' => null,
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
                'logo' => 'https://www.google.com/s2/favicons?domain=kiro.dev&sz=128',
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
                'logo' => 'https://www.google.com/s2/favicons?domain=codeium.com&sz=128',
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
                'logo' => 'https://www.google.com/s2/favicons?domain=zed.dev&sz=128',
            ],
            [
                'name' => 'GitHub Copilot',
                'slug' => 'github-copilot',
                'description' => "GitHub's AI pair programmer in VS Code",
                'website' => 'https://github.com/features/copilot',
                'docs_url' => 'https://docs.github.com/copilot',
                'github_url' => null,
                'supported_config_types' => ['mcp-servers', 'rules', 'skills', 'prompts'],
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
                'logo' => 'https://github.githubassets.com/assets/GitHub-Mark-ea2971cee799.png',
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
