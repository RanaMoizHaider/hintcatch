<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Category;
use App\Models\Config;
use App\Models\ConfigFile;
use App\Models\ConfigType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    private User $systemUser;

    public function run(): void
    {
        $this->systemUser = User::where('username', 'ranamoizhaider')->first();

        $this->seedRulesConfigs();
        $this->seedSlashCommands();
        $this->seedPlugins();
    }

    private function seedRulesConfigs(): void
    {
        $rulesType = ConfigType::where('slug', 'rules')->first();
        $category = Category::first();

        if (! $rulesType || ! $category) {
            return;
        }

        $configs = $this->getRulesConfigsData();

        foreach ($configs as $configData) {
            $agent = Agent::where('slug', $configData['agent_slug'])->first();
            if (! $agent) {
                continue;
            }

            $config = Config::updateOrCreate(
                ['slug' => $configData['slug']],
                [
                    'name' => $configData['name'],
                    'slug' => $configData['slug'],
                    'description' => $configData['description'],
                    'config_type_id' => $rulesType->id,
                    'agent_id' => $agent->id,
                    'submitted_by' => $this->systemUser->id,
                    'category_id' => $category->id,
                    'source_url' => $configData['source_url'] ?? null,
                    'source_author' => $configData['source_author'] ?? null,
                    'is_featured' => $configData['is_featured'] ?? false,
                ]
            );

            foreach ($configData['files'] as $file) {
                ConfigFile::updateOrCreate(
                    ['config_id' => $config->id, 'filename' => $file['filename']],
                    $file
                );
            }
        }
    }

    private function seedSlashCommands(): void
    {
        $commandsType = ConfigType::where('slug', 'slash-commands')->first();
        $category = Category::first();

        if (! $commandsType || ! $category) {
            return;
        }

        $configs = $this->getSlashCommandsData();

        foreach ($configs as $configData) {
            $agent = Agent::where('slug', $configData['agent_slug'])->first();
            if (! $agent) {
                continue;
            }

            $config = Config::updateOrCreate(
                ['slug' => $configData['slug']],
                [
                    'name' => $configData['name'],
                    'slug' => $configData['slug'],
                    'description' => $configData['description'],
                    'config_type_id' => $commandsType->id,
                    'agent_id' => $agent->id,
                    'submitted_by' => $this->systemUser->id,
                    'category_id' => $category->id,
                    'source_url' => $configData['source_url'] ?? null,
                    'source_author' => $configData['source_author'] ?? null,
                ]
            );

            foreach ($configData['files'] as $file) {
                ConfigFile::updateOrCreate(
                    ['config_id' => $config->id, 'filename' => $file['filename']],
                    $file
                );
            }
        }
    }

    private function getRulesConfigsData(): array
    {
        return [
            [
                'agent_slug' => 'opencode',
                'name' => 'Laravel Best Practices',
                'slug' => 'opencode-laravel-rules',
                'description' => 'Comprehensive Laravel development rules for OpenCode',
                'source_url' => 'https://github.com/sst/opencode',
                'source_author' => 'SST',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'AGENTS.md',
                        'path' => 'AGENTS.md',
                        'content' => $this->getLaravelRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Claude Code TypeScript Rules',
                'slug' => 'claude-code-typescript-rules',
                'description' => 'TypeScript and React development rules for Claude Code',
                'source_url' => 'https://docs.anthropic.com/en/docs/claude-code',
                'source_author' => 'Anthropic',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'CLAUDE.md',
                        'path' => 'CLAUDE.md',
                        'content' => $this->getTypeScriptRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'cursor',
                'name' => 'Cursor React Rules',
                'slug' => 'cursor-react-rules',
                'description' => 'React and Next.js development rules for Cursor',
                'source_url' => 'https://cursor.directory',
                'source_author' => 'Cursor Community',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => '.cursorrules',
                        'path' => '.cursorrules',
                        'content' => $this->getCursorReactRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'windsurf',
                'name' => 'Windsurf Python Rules',
                'slug' => 'windsurf-python-rules',
                'description' => 'Python development rules for Windsurf IDE',
                'source_url' => 'https://docs.codeium.com/windsurf',
                'source_author' => 'Codeium',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => '.windsurfrules',
                        'path' => '.windsurfrules',
                        'content' => $this->getWindsurfPythonRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'cline',
                'name' => 'Cline Full Stack Rules',
                'slug' => 'cline-fullstack-rules',
                'description' => 'Full stack development rules for Cline',
                'source_url' => 'https://github.com/cline/cline',
                'source_author' => 'Cline',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => '.clinerules',
                        'path' => '.clinerules',
                        'content' => $this->getClineFullStackRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Gemini CLI Go Rules',
                'slug' => 'gemini-cli-go-rules',
                'description' => 'Go development rules for Gemini CLI',
                'source_url' => 'https://github.com/google-gemini/gemini-cli',
                'source_author' => 'Google',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'GEMINI.md',
                        'path' => 'GEMINI.md',
                        'content' => $this->getGeminiGoRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Codex Node.js Rules',
                'slug' => 'codex-nodejs-rules',
                'description' => 'Node.js development rules for OpenAI Codex',
                'source_url' => 'https://github.com/openai/codex',
                'source_author' => 'OpenAI',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'AGENTS.md',
                        'path' => 'AGENTS.md',
                        'content' => $this->getCodexNodeRulesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'github-copilot',
                'name' => 'Copilot Instructions',
                'slug' => 'copilot-instructions-general',
                'description' => 'General coding instructions for GitHub Copilot',
                'source_url' => 'https://docs.github.com/copilot',
                'source_author' => 'GitHub',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'copilot-instructions.md',
                        'path' => '.github/copilot-instructions.md',
                        'content' => $this->getCopilotInstructionsContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'aider',
                'name' => 'Aider Convention File',
                'slug' => 'aider-conventions',
                'description' => 'Coding conventions for Aider AI pair programmer',
                'source_url' => 'https://aider.chat/docs',
                'source_author' => 'Paul Gauthier',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => '.aider',
                        'path' => '.aider',
                        'content' => $this->getAiderConventionsContent(),
                        'language' => 'yaml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'zed',
                'name' => 'Zed Rust Rules',
                'slug' => 'zed-rust-rules',
                'description' => 'Rust development rules for Zed editor',
                'source_url' => 'https://zed.dev/docs',
                'source_author' => 'Zed Industries',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'settings.json',
                        'path' => '.zed/settings.json',
                        'content' => $this->getZedRustSettingsContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'kiro',
                'name' => 'Kiro AWS Rules',
                'slug' => 'kiro-aws-rules',
                'description' => 'AWS development rules for Kiro IDE',
                'source_url' => 'https://kiro.dev/docs',
                'source_author' => 'AWS',
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'settings.json',
                        'path' => '.kiro/settings.json',
                        'content' => $this->getKiroAwsSettingsContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    private function getSlashCommandsData(): array
    {
        return [
            [
                'agent_slug' => 'opencode',
                'name' => 'Commit Command',
                'slug' => 'opencode-commit-command',
                'description' => 'Smart git commit with conventional commits',
                'source_url' => 'https://github.com/sst/opencode',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'commit.md',
                        'path' => '.opencode/command/commit.md',
                        'content' => "---\ndescription: Create a git commit\n---\n\nCreate a commit with conventional commits format.",
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'PR Review Command',
                'slug' => 'claude-code-pr-review',
                'description' => 'Automated pull request review',
                'source_url' => 'https://docs.anthropic.com/en/docs/claude-code',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'pr-review.md',
                        'path' => '.claude/commands/pr-review.md',
                        'content' => "---\ndescription: Review PR changes\n---\n\nReview the current pull request for bugs and issues.",
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Agentic Commands (OpenCode) - https://github.com/Cluster444/agentic
            [
                'agent_slug' => 'opencode',
                'name' => 'Ticket Command',
                'slug' => 'agentic-ticket-command',
                'description' => 'Create and manage development tickets with structured requirements',
                'source_url' => 'https://github.com/Cluster444/agentic',
                'source_author' => 'Chris Covington',
                'files' => [
                    [
                        'filename' => 'ticket.md',
                        'path' => '.opencode/command/ticket.md',
                        'content' => $this->getAgenticTicketContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Research Command',
                'slug' => 'agentic-research-command',
                'description' => 'Research a topic or codebase area before implementation',
                'source_url' => 'https://github.com/Cluster444/agentic',
                'source_author' => 'Chris Covington',
                'files' => [
                    [
                        'filename' => 'research.md',
                        'path' => '.opencode/command/research.md',
                        'content' => $this->getAgenticResearchContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Plan Command',
                'slug' => 'agentic-plan-command',
                'description' => 'Create a detailed implementation plan for a feature or fix',
                'source_url' => 'https://github.com/Cluster444/agentic',
                'source_author' => 'Chris Covington',
                'files' => [
                    [
                        'filename' => 'plan.md',
                        'path' => '.opencode/command/plan.md',
                        'content' => $this->getAgenticPlanContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Execute Command',
                'slug' => 'agentic-execute-command',
                'description' => 'Execute the current plan step by step',
                'source_url' => 'https://github.com/Cluster444/agentic',
                'source_author' => 'Chris Covington',
                'files' => [
                    [
                        'filename' => 'execute.md',
                        'path' => '.opencode/command/execute.md',
                        'content' => $this->getAgenticExecuteContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Review Command',
                'slug' => 'agentic-review-command',
                'description' => 'Review completed work and suggest improvements',
                'source_url' => 'https://github.com/Cluster444/agentic',
                'source_author' => 'Chris Covington',
                'files' => [
                    [
                        'filename' => 'review.md',
                        'path' => '.opencode/command/review.md',
                        'content' => $this->getAgenticReviewContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // TÂCHES Commands (OpenCode Port) - https://github.com/stephenschoettler/taches-oc-prompts
            [
                'agent_slug' => 'opencode',
                'name' => 'Create Prompt Command',
                'slug' => 'taches-oc-create-prompt',
                'description' => 'Create a new meta-prompt using the TÂCHES framework',
                'source_url' => 'https://github.com/stephenschoettler/taches-oc-prompts',
                'source_author' => 'Stephen Schoettler',
                'files' => [
                    [
                        'filename' => 'create-prompt.md',
                        'path' => '.opencode/command/create-prompt.md',
                        'content' => $this->getTachesOCCreatePromptContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Run Prompt Command',
                'slug' => 'taches-oc-run-prompt',
                'description' => 'Run an existing meta-prompt with parameters',
                'source_url' => 'https://github.com/stephenschoettler/taches-oc-prompts',
                'source_author' => 'Stephen Schoettler',
                'files' => [
                    [
                        'filename' => 'run-prompt.md',
                        'path' => '.opencode/command/run-prompt.md',
                        'content' => $this->getTachesOCRunPromptContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Add To Todos Command',
                'slug' => 'taches-oc-add-to-todos',
                'description' => 'Add tasks to the todo list from natural language',
                'source_url' => 'https://github.com/stephenschoettler/taches-oc-prompts',
                'source_author' => 'Stephen Schoettler',
                'files' => [
                    [
                        'filename' => 'add-to-todos.md',
                        'path' => '.opencode/command/add-to-todos.md',
                        'content' => $this->getTachesOCAddToTodosContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Check Todos Command',
                'slug' => 'taches-oc-check-todos',
                'description' => 'Review and update todo list status',
                'source_url' => 'https://github.com/stephenschoettler/taches-oc-prompts',
                'source_author' => 'Stephen Schoettler',
                'files' => [
                    [
                        'filename' => 'check-todos.md',
                        'path' => '.opencode/command/check-todos.md',
                        'content' => $this->getTachesOCCheckTodosContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Whats Next Command',
                'slug' => 'taches-oc-whats-next',
                'description' => 'Get the next task to work on based on priority',
                'source_url' => 'https://github.com/stephenschoettler/taches-oc-prompts',
                'source_author' => 'Stephen Schoettler',
                'files' => [
                    [
                        'filename' => 'whats-next.md',
                        'path' => '.opencode/command/whats-next.md',
                        'content' => $this->getTachesOCWhatsNextContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // TÂCHES Commands (Claude Code) - https://github.com/glittercowboy/taches-cc-resources
            [
                'agent_slug' => 'claude-code',
                'name' => 'Create Agent Skill',
                'slug' => 'taches-cc-create-agent-skill',
                'description' => 'Create a new agent skill with proper structure',
                'source_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'source_author' => 'Lex Christopherson',
                'files' => [
                    [
                        'filename' => 'create-agent-skill.md',
                        'path' => '.claude/commands/create-agent-skill.md',
                        'content' => $this->getTachesCCCreateAgentSkillContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Create Hook',
                'slug' => 'taches-cc-create-hook',
                'description' => 'Create a lifecycle hook for automation',
                'source_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'source_author' => 'Lex Christopherson',
                'files' => [
                    [
                        'filename' => 'create-hook.md',
                        'path' => '.claude/commands/create-hook.md',
                        'content' => $this->getTachesCCCreateHookContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Create Meta Prompt',
                'slug' => 'taches-cc-create-meta-prompt',
                'description' => 'Create a meta-prompt for generating other prompts',
                'source_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'source_author' => 'Lex Christopherson',
                'files' => [
                    [
                        'filename' => 'create-meta-prompt.md',
                        'path' => '.claude/commands/create-meta-prompt.md',
                        'content' => $this->getTachesCCCreateMetaPromptContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Debug Command',
                'slug' => 'taches-cc-debug',
                'description' => 'Expert debugging with systematic approach',
                'source_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'source_author' => 'Lex Christopherson',
                'files' => [
                    [
                        'filename' => 'debug.md',
                        'path' => '.claude/commands/debug.md',
                        'content' => $this->getTachesCCDebugContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Create Plan',
                'slug' => 'taches-cc-create-plan',
                'description' => 'Create a structured execution plan',
                'source_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'source_author' => 'Lex Christopherson',
                'files' => [
                    [
                        'filename' => 'create-plan.md',
                        'path' => '.claude/commands/create-plan.md',
                        'content' => $this->getTachesCCCreatePlanContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Claude Codex Settings Commands - https://github.com/fcakyon/claude-codex-settings
            [
                'agent_slug' => 'claude-code',
                'name' => 'Commit Staged',
                'slug' => 'codex-commit-staged',
                'description' => 'Create a commit from staged changes with conventional format',
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'files' => [
                    [
                        'filename' => 'commit-staged.md',
                        'path' => '.claude/commands/commit-staged.md',
                        'content' => $this->getCodexCommitStagedContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Create PR',
                'slug' => 'codex-create-pr',
                'description' => 'Create a pull request with auto-generated description',
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'files' => [
                    [
                        'filename' => 'create-pr.md',
                        'path' => '.claude/commands/create-pr.md',
                        'content' => $this->getCodexCreatePRContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Review PR',
                'slug' => 'codex-review-pr',
                'description' => 'Review a pull request with detailed feedback',
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'files' => [
                    [
                        'filename' => 'review-pr.md',
                        'path' => '.claude/commands/review-pr.md',
                        'content' => $this->getCodexReviewPRContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Load Claude MD',
                'slug' => 'codex-load-claude-md',
                'description' => 'Load and apply CLAUDE.md rules to the session',
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'files' => [
                    [
                        'filename' => 'load-claude-md.md',
                        'path' => '.claude/commands/load-claude-md.md',
                        'content' => $this->getCodexLoadClaudeMDContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Commit Staged (Codex)',
                'slug' => 'codex-cli-commit-staged',
                'description' => 'Create a commit from staged changes for OpenAI Codex',
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'files' => [
                    [
                        'filename' => 'commit-staged.md',
                        'path' => 'AGENTS.md',
                        'content' => $this->getCodexCLICommitStagedContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    private function getLaravelRulesContent(): string
    {
        return <<<'MD'
# Laravel Development Rules

## Code Style
- Follow PSR-12 coding standards
- Use type hints and return types
- Use constructor property promotion

## Architecture
- Use Form Request classes for validation
- Use Eloquent over raw queries
- Use queued jobs for heavy operations

## Testing
- Write feature tests for all endpoints
- Use factories for test data
MD;
    }

    private function getTypeScriptRulesContent(): string
    {
        return <<<'MD'
# TypeScript Development Rules

## Code Style
- Use strict TypeScript configuration
- Prefer interfaces over types for objects
- Use const assertions where appropriate

## React
- Use functional components with hooks
- Prefer composition over inheritance
- Use React.memo for expensive components
MD;
    }

    private function getCursorReactRulesContent(): string
    {
        return <<<'MD'
# React Development Rules

You are an expert React developer.

## Guidelines
- Use functional components
- Use TypeScript for type safety
- Follow React hooks best practices
- Use Tailwind CSS for styling
MD;
    }

    private function getWindsurfPythonRulesContent(): string
    {
        return <<<'MD'
# Python Development Rules

You are an expert Python developer.

## Code Style
- Follow PEP 8 style guide
- Use type hints (Python 3.9+)
- Use dataclasses for data containers

## Best Practices
- Use virtual environments
- Write docstrings for all functions
- Use pytest for testing
MD;
    }

    private function getClineFullStackRulesContent(): string
    {
        return <<<'MD'
# Full Stack Development Rules

## Frontend
- React with TypeScript
- Tailwind CSS for styling
- Zustand for state management

## Backend
- Node.js with Express or Fastify
- PostgreSQL for database
- Prisma for ORM
MD;
    }

    private function getGeminiGoRulesContent(): string
    {
        return <<<'MD'
# Go Development Rules

## Code Style
- Follow Go conventions (gofmt)
- Use meaningful variable names
- Keep functions small and focused

## Best Practices
- Handle all errors explicitly
- Use interfaces for abstraction
- Write table-driven tests
MD;
    }

    private function getCodexNodeRulesContent(): string
    {
        return <<<'MD'
# Node.js Development Rules

## Code Style
- Use ESM modules
- Use async/await over callbacks
- Use TypeScript when possible

## Best Practices
- Handle errors properly
- Use environment variables for config
- Write unit and integration tests
MD;
    }

    private function getCopilotInstructionsContent(): string
    {
        return <<<'MD'
# GitHub Copilot Instructions

## General Guidelines
- Write clean, readable code
- Follow language-specific conventions
- Add comments for complex logic
- Write tests for new features
MD;
    }

    private function getAiderConventionsContent(): string
    {
        return <<<'YAML'
# Aider conventions
convention-file: .aider

# Code style
style:
  - Use consistent formatting
  - Follow project conventions
  - Write clear commit messages
YAML;
    }

    private function getZedRustSettingsContent(): string
    {
        return <<<'JSON'
{
  "assistant": {
    "default_model": {
      "provider": "anthropic",
      "model": "claude-sonnet-4-20250514"
    }
  },
  "languages": {
    "Rust": {
      "format_on_save": "on"
    }
  }
}
JSON;
    }

    private function getKiroAwsSettingsContent(): string
    {
        return <<<'JSON'
{
  "ai": {
    "enabled": true
  },
  "specs": {
    "enabled": true,
    "autoGenerate": true
  }
}
JSON;
    }

    // Agentic Command Content Methods
    private function getAgenticTicketContent(): string
    {
        return <<<'MD'
---
description: Create a development ticket
argument-hint: <ticket-description>
---

# Ticket Command

Create a structured development ticket with:

1. **Title**: Clear, concise summary
2. **Description**: Detailed requirements
3. **Acceptance Criteria**: Specific conditions for completion
4. **Technical Notes**: Implementation considerations

## Process

1. Gather requirements from the description
2. Break down into actionable items
3. Identify dependencies
4. Estimate complexity
5. Create formatted ticket
MD;
    }

    private function getAgenticResearchContent(): string
    {
        return <<<'MD'
---
description: Research a topic or codebase area
argument-hint: <topic>
---

# Research Command

Perform thorough research on the specified topic.

## Research Areas

- Codebase patterns and conventions
- External documentation
- Best practices
- Implementation examples

## Output

Provide a comprehensive research summary with:
- Key findings
- Relevant code examples
- Recommendations
- Sources and references
MD;
    }

    private function getAgenticPlanContent(): string
    {
        return <<<'MD'
---
description: Create an implementation plan
argument-hint: <feature-or-fix>
---

# Plan Command

Create a detailed implementation plan.

## Plan Structure

1. **Objective**: Clear goal statement
2. **Scope**: What's included and excluded
3. **Steps**: Ordered implementation tasks
4. **Validation**: How to verify success
5. **Risks**: Potential issues and mitigations

## Output

A structured plan ready for execution.
MD;
    }

    private function getAgenticExecuteContent(): string
    {
        return <<<'MD'
---
description: Execute the current plan
---

# Execute Command

Execute the implementation plan step by step.

## Process

1. Review the current plan
2. Identify the next incomplete step
3. Execute the step
4. Validate the result
5. Update progress
6. Proceed to next step or report completion

## Guidelines

- Complete one step at a time
- Validate before proceeding
- Document any deviations
- Report blockers immediately
MD;
    }

    private function getAgenticReviewContent(): string
    {
        return <<<'MD'
---
description: Review completed work
---

# Review Command

Review completed work and suggest improvements.

## Review Checklist

- [ ] Code follows conventions
- [ ] Tests are comprehensive
- [ ] Documentation is updated
- [ ] No security issues
- [ ] Performance is acceptable

## Output

- Summary of changes
- Issues found
- Improvement suggestions
- Approval status
MD;
    }

    // TÂCHES OpenCode Command Content Methods
    private function getTachesOCCreatePromptContent(): string
    {
        return <<<'MD'
---
description: Create a new meta-prompt
argument-hint: <prompt-name>
---

# Create Prompt

Create a new meta-prompt using the TÂCHES framework.

## Prompt Structure

1. Purpose and scope
2. Input parameters
3. Processing instructions
4. Output format
5. Examples

## Guidelines

- Clear, specific instructions
- Well-defined parameters
- Comprehensive examples
MD;
    }

    private function getTachesOCRunPromptContent(): string
    {
        return <<<'MD'
---
description: Run an existing meta-prompt
argument-hint: <prompt-name> [parameters]
---

# Run Prompt

Execute an existing meta-prompt with parameters.

## Usage

Provide the prompt name and any required parameters.
The prompt will be loaded and executed with your inputs.
MD;
    }

    private function getTachesOCAddToTodosContent(): string
    {
        return <<<'MD'
---
description: Add tasks to the todo list
argument-hint: <task-description>
---

# Add To Todos

Parse natural language and add tasks to the todo list.

## Examples

- "Add authentication feature" -> Creates todo with details
- "Fix the login bug and update tests" -> Creates multiple todos

## Output

Confirm tasks added with IDs and priorities.
MD;
    }

    private function getTachesOCCheckTodosContent(): string
    {
        return <<<'MD'
---
description: Review todo list status
---

# Check Todos

Review and update the current todo list.

## Actions

- Show all todos with status
- Mark completed items
- Update priorities
- Remove cancelled items
MD;
    }

    private function getTachesOCWhatsNextContent(): string
    {
        return <<<'MD'
---
description: Get the next task to work on
---

# What's Next

Analyze the todo list and recommend the next task.

## Priority Factors

1. Explicit priority level
2. Dependencies resolved
3. Estimated effort
4. Impact on project

## Output

Recommended next task with rationale.
MD;
    }

    // TÂCHES Claude Code Command Content Methods
    private function getTachesCCCreateAgentSkillContent(): string
    {
        return <<<'MD'
---
description: Create a new agent skill
argument-hint: <skill-name>
---

# Create Agent Skill

Create a new agent skill with proper structure.

## Skill Components

1. Skill definition file
2. Instructions and guidelines
3. Tool allowlist
4. Examples and references

## Output

Complete skill directory with all required files.
MD;
    }

    private function getTachesCCCreateHookContent(): string
    {
        return <<<'MD'
---
description: Create a lifecycle hook
argument-hint: <hook-name>
---

# Create Hook

Create a lifecycle hook for automation.

## Hook Types

- PreMessage
- PostMessage
- PreTool
- PostTool

## Output

Hook configuration file ready for use.
MD;
    }

    private function getTachesCCCreateMetaPromptContent(): string
    {
        return <<<'MD'
---
description: Create a meta-prompt
argument-hint: <prompt-name>
---

# Create Meta Prompt

Create a meta-prompt for generating other prompts.

## Template Structure

- Variables and placeholders
- Generation rules
- Output constraints
- Examples
MD;
    }

    private function getTachesCCDebugContent(): string
    {
        return <<<'MD'
---
description: Debug with expert methodology
argument-hint: <issue-description>
---

# Debug

Apply expert debugging methodology.

## Process

1. **Reproduce**: Confirm the issue
2. **Isolate**: Narrow the scope
3. **Analyze**: Examine evidence
4. **Hypothesize**: Form theories
5. **Test**: Verify hypothesis
6. **Fix**: Apply minimal fix
7. **Validate**: Confirm resolution
MD;
    }

    private function getTachesCCCreatePlanContent(): string
    {
        return <<<'MD'
---
description: Create an execution plan
argument-hint: <goal>
---

# Create Plan

Create a structured execution plan.

## Plan Components

- Phases and milestones
- Tasks and subtasks
- Dependencies
- Success criteria
- Risk mitigation
MD;
    }

    // Claude Codex Settings Command Content Methods
    private function getCodexCommitStagedContent(): string
    {
        return <<<'MD'
---
description: Commit staged changes
---

# Commit Staged

Create a commit from staged changes.

## Process

1. Review staged changes
2. Analyze change types
3. Generate conventional commit message
4. Create commit

## Commit Format

```
<type>(<scope>): <description>

<body>

<footer>
```
MD;
    }

    private function getCodexCreatePRContent(): string
    {
        return <<<'MD'
---
description: Create a pull request
---

# Create PR

Create a pull request with auto-generated description.

## Process

1. Analyze commits since branch
2. Generate PR title
3. Create description with changes
4. Submit PR via gh cli

## Output

PR URL and summary.
MD;
    }

    private function getCodexReviewPRContent(): string
    {
        return <<<'MD'
---
description: Review a pull request
argument-hint: [pr-number]
---

# Review PR

Provide detailed review of a pull request.

## Review Areas

- Code quality
- Logic correctness
- Security concerns
- Performance impact
- Test coverage

## Output

Structured review with actionable feedback.
MD;
    }

    private function getCodexLoadClaudeMDContent(): string
    {
        return <<<'MD'
---
description: Load CLAUDE.md rules
---

# Load Claude MD

Load and apply CLAUDE.md rules to the session.

## Process

1. Find CLAUDE.md in project root
2. Parse rules and guidelines
3. Apply to current context
4. Confirm loaded rules
MD;
    }

    private function getCodexCLICommitStagedContent(): string
    {
        return <<<'MD'
# Commit Staged (Codex CLI)

Create commits from staged changes using OpenAI Codex.

## Usage

Run this command after staging changes with `git add`.

## Process

1. Analyze staged diff
2. Generate commit message
3. Create commit

## Format

Uses conventional commits format.
MD;
    }

    private function seedPlugins(): void
    {
        $pluginsType = ConfigType::where('slug', 'plugins')->first();
        $category = Category::first();

        if (! $pluginsType || ! $category) {
            return;
        }

        $configs = $this->getPluginsData();

        foreach ($configs as $configData) {
            $agent = Agent::where('slug', $configData['agent_slug'])->first();
            if (! $agent) {
                continue;
            }

            $config = Config::updateOrCreate(
                ['slug' => $configData['slug']],
                [
                    'name' => $configData['name'],
                    'slug' => $configData['slug'],
                    'description' => $configData['description'],
                    'config_type_id' => $pluginsType->id,
                    'agent_id' => $agent->id,
                    'submitted_by' => $this->systemUser->id,
                    'category_id' => $category->id,
                    'source_url' => $configData['source_url'] ?? null,
                    'source_author' => $configData['source_author'] ?? null,
                    'uses_standard_install' => $configData['uses_standard_install'] ?? true,
                    'readme' => $configData['readme'] ?? null,
                ]
            );

            if (isset($configData['files'])) {
                foreach ($configData['files'] as $file) {
                    ConfigFile::updateOrCreate(
                        ['config_id' => $config->id, 'filename' => $file['filename']],
                        $file
                    );
                }
            }
        }
    }

    /**
     * @return array<int, array{agent_slug: string, name: string, slug: string, description: string, source_url: string, source_author: string, uses_standard_install: bool, instructions?: string, files: array<int, array{filename: string, path: string, content: string, language: string, is_primary: bool, order: int}>}>
     */
    private function getPluginsData(): array
    {
        return [
            // OpenCode Plugins
            [
                'agent_slug' => 'opencode',
                'name' => 'Zellij Session Namer',
                'slug' => 'opencode-zellij-namer',
                'description' => 'AI-powered dynamic Zellij session naming. Generates contextual names like project-intent-tag (e.g. myapp-feat-auth) using Gemini AI.',
                'source_url' => 'https://github.com/24601/opencode-zellij-namer',
                'source_author' => '24601',
                'uses_standard_install' => true,
                'files' => [
                    [
                        'filename' => 'opencode.json',
                        'path' => 'opencode.json',
                        'content' => $this->getZellijNamerConfigContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Helicone Session',
                'slug' => 'opencode-helicone-session',
                'description' => 'Auto-injects Helicone session headers (Helicone-Session-Id, Helicone-Session-Name) for LLM request grouping and observability.',
                'source_url' => 'https://github.com/H2Shami/opencode-helicone-session',
                'source_author' => 'H2Shami',
                'uses_standard_install' => true,
                'files' => [
                    [
                        'filename' => 'opencode.json',
                        'path' => 'opencode.json',
                        'content' => $this->getHeliconeSessionConfigContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Claude Code Plugins (Official Marketplace)
            [
                'agent_slug' => 'claude-code',
                'name' => 'GitHub Integration',
                'slug' => 'claude-code-github',
                'description' => 'Official GitHub integration for Claude Code. Work with issues, pull requests, repositories, and code reviews directly from Claude.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('github'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Linear Integration',
                'slug' => 'claude-code-linear',
                'description' => 'Official Linear integration for Claude Code. Manage issues, projects, and workflows directly from your coding session.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('linear'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Commit Commands',
                'slug' => 'claude-code-commit-commands',
                'description' => 'Enhanced git commit workflows for Claude Code. Generate conventional commits, interactive staging, and smart commit messages.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('commit-commands'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'PR Review Toolkit',
                'slug' => 'claude-code-pr-review-toolkit',
                'description' => 'Comprehensive pull request review toolkit. Automated code review, security scanning, and actionable feedback.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('pr-review-toolkit'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'TypeScript LSP',
                'slug' => 'claude-code-typescript-lsp',
                'description' => 'TypeScript Language Server Protocol integration. Enhanced code intelligence, type checking, and refactoring support.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('typescript-lsp'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Pyright LSP',
                'slug' => 'claude-code-pyright-lsp',
                'description' => 'Python type checking and language server integration via Pyright. Static type analysis and intelligent code completion.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('pyright-lsp'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Notion Integration',
                'slug' => 'claude-code-notion',
                'description' => 'Official Notion integration for Claude Code. Access and update Notion pages, databases, and documentation.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('notion'),
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Sentry Integration',
                'slug' => 'claude-code-sentry',
                'description' => 'Sentry error tracking integration. View and debug production errors directly from your coding session.',
                'source_url' => 'https://github.com/anthropics/claude-code-plugins',
                'source_author' => 'Anthropic',
                'uses_standard_install' => true,
                'readme' => $this->getClaudePluginInstallContent('sentry'),
            ],
        ];
    }

    private function getZellijNamerConfigContent(): string
    {
        return <<<'JSON'
{
  "plugin": [
    "opencode-zellij-namer"
  ]
}
JSON;
    }

    private function getHeliconeSessionConfigContent(): string
    {
        return <<<'JSON'
{
  "plugin": [
    "opencode-helicone-session"
  ]
}
JSON;
    }

    private function getClaudePluginInstallContent(string $pluginName): string
    {
        return <<<MD
# Installation

Install from the official Claude Code marketplace:

```
/plugin install {$pluginName}@claude-plugins-official
```

## Scopes

You can install plugins at different scopes:
- **user**: Available in all your projects
- **project**: Available only in this project (saved to `.claude/plugins.json`)
- **local**: Available only in this session

To specify scope:
```
/plugin install {$pluginName}@claude-plugins-official --scope user
```
MD;
    }
}
