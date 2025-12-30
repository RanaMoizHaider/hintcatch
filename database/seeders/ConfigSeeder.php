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
        $this->seedCustomTools();
        $this->seedHooks();
        $this->seedAgents();
        $this->seedSkills();
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
            // OpenAI Codex CLI Custom Prompts
            [
                'agent_slug' => 'codex',
                'name' => 'Draft PR Command',
                'slug' => 'codex-draft-pr',
                'description' => 'Create a branch, commit staged changes, and open a draft PR',
                'source_url' => 'https://developers.openai.com/codex/guides/slash-commands',
                'source_author' => 'OpenAI',
                'files' => [
                    [
                        'filename' => 'draftpr.md',
                        'path' => '~/.codex/prompts/draftpr.md',
                        'content' => $this->getCodexDraftPRContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Code Review Command',
                'slug' => 'codex-code-review',
                'description' => 'Review code changes with detailed feedback on specific files',
                'source_url' => 'https://developers.openai.com/codex/guides/slash-commands',
                'source_author' => 'OpenAI',
                'files' => [
                    [
                        'filename' => 'review.md',
                        'path' => '~/.codex/prompts/review.md',
                        'content' => $this->getCodexReviewCommandContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Test Generator Command',
                'slug' => 'codex-test-generator',
                'description' => 'Generate comprehensive tests for specified files or functions',
                'source_url' => 'https://developers.openai.com/codex/guides/slash-commands',
                'source_author' => 'OpenAI',
                'files' => [
                    [
                        'filename' => 'gentest.md',
                        'path' => '~/.codex/prompts/gentest.md',
                        'content' => $this->getCodexTestGeneratorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Refactor Command',
                'slug' => 'codex-refactor',
                'description' => 'Refactor code with a specific focus area like performance or readability',
                'source_url' => 'https://developers.openai.com/codex/guides/slash-commands',
                'source_author' => 'OpenAI',
                'files' => [
                    [
                        'filename' => 'refactor.md',
                        'path' => '~/.codex/prompts/refactor.md',
                        'content' => $this->getCodexRefactorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'codex',
                'name' => 'Document Code Command',
                'slug' => 'codex-document',
                'description' => 'Add comprehensive documentation to code files',
                'source_url' => 'https://developers.openai.com/codex/guides/slash-commands',
                'source_author' => 'OpenAI',
                'files' => [
                    [
                        'filename' => 'document.md',
                        'path' => '~/.codex/prompts/document.md',
                        'content' => $this->getCodexDocumentContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Gemini CLI Custom Commands (TOML format)
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Changelog Command',
                'slug' => 'gemini-changelog',
                'description' => 'Adds a new entry to the project\'s CHANGELOG.md file following Keep a Changelog format',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'changelog.toml',
                        'path' => '.gemini/commands/changelog.toml',
                        'content' => $this->getGeminiChangelogContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Git Commit Command',
                'slug' => 'gemini-git-commit',
                'description' => 'Generates a Git commit message based on staged changes using shell injection',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'commit.toml',
                        'path' => '.gemini/commands/git/commit.toml',
                        'content' => $this->getGeminiGitCommitContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Git Fix Command',
                'slug' => 'gemini-git-fix',
                'description' => 'Generates a code fix for a given issue description with {{args}} injection',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'fix.toml',
                        'path' => '.gemini/commands/git/fix.toml',
                        'content' => $this->getGeminiGitFixContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Grep Code Command',
                'slug' => 'gemini-grep-code',
                'description' => 'Search codebase with grep and summarize findings using shell-escaped arguments',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'grep-code.toml',
                        'path' => '.gemini/commands/grep-code.toml',
                        'content' => $this->getGeminiGrepCodeContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Code Review Command',
                'slug' => 'gemini-review',
                'description' => 'Reviews code using a best practices guide with file content injection',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'review.toml',
                        'path' => '.gemini/commands/review.toml',
                        'content' => $this->getGeminiReviewContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'gemini-cli',
                'name' => 'Refactor Pure Function',
                'slug' => 'gemini-refactor-pure',
                'description' => 'Refactors code into a pure function with explanation of changes',
                'source_url' => 'https://geminicli.com/docs/cli/custom-commands',
                'source_author' => 'Google',
                'files' => [
                    [
                        'filename' => 'pure.toml',
                        'path' => '~/.gemini/commands/refactor/pure.toml',
                        'content' => $this->getGeminiRefactorPureContent(),
                        'language' => 'toml',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Factory Droid CLI Custom Commands (Markdown format)
            [
                'agent_slug' => 'droid',
                'name' => 'Code Review Command',
                'slug' => 'droid-code-review',
                'description' => 'Review code changes for quality, security, and best practices with structured feedback',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-slash-commands',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'review.md',
                        'path' => '.factory/commands/review.md',
                        'content' => $this->getDroidCodeReviewContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Commit Command',
                'slug' => 'droid-commit',
                'description' => 'Generate conventional commit messages from staged changes with smart analysis',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-slash-commands',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'commit.md',
                        'path' => '.factory/commands/commit.md',
                        'content' => $this->getDroidCommitContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Standup Helper Command',
                'slug' => 'droid-standup',
                'description' => 'Generate daily standup summaries from recent git activity and todo items',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-slash-commands',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'standup.md',
                        'path' => '.factory/commands/standup.md',
                        'content' => $this->getDroidStandupContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Smoke Test Command',
                'slug' => 'droid-smoke-test',
                'description' => 'Run quick smoke tests on recent changes to verify basic functionality',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-slash-commands',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'smoke-test.md',
                        'path' => '.factory/commands/smoke-test.md',
                        'content' => $this->getDroidSmokeTestContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    // Factory Droid Command Content Methods
    private function getDroidCodeReviewContent(): string
    {
        return <<<'MD'
---
description: Review code for quality and security
argument-hint: [files or git ref]
---

# Code Review

Review code changes for quality, security, and best practices.

## Process

1. If no argument provided, review staged changes (`git diff --staged`)
2. If file paths provided, review those specific files
3. If git ref provided (branch, commit), compare against current HEAD

## Review Criteria

### Code Quality
- Clear, readable code with good naming
- No code duplication (DRY)
- Proper error handling
- Appropriate comments for complex logic

### Security
- Input validation and sanitization
- No hardcoded secrets or credentials
- Proper authentication/authorization checks
- SQL injection and XSS prevention

### Performance
- Efficient algorithms and data structures
- No N+1 query problems
- Appropriate caching strategies
- Resource cleanup (connections, files)

### Best Practices
- Follows project conventions
- Proper typing and documentation
- Test coverage for new code
- Backward compatibility considerations

## Output Format

Organize feedback by severity:

### 🔴 Critical (Must Fix)
Security vulnerabilities, data loss risks, breaking bugs

### 🟡 Warning (Should Fix)
Code smells, performance issues, missing error handling

### 🟢 Suggestion (Consider)
Readability improvements, minor optimizations

Include file:line references and specific fix examples.
MD;
    }

    private function getDroidCommitContent(): string
    {
        return <<<'MD'
---
description: Generate commit message from staged changes
---

# Smart Commit

Generate a conventional commit message from staged changes.

## Process

1. Run `git diff --staged` to analyze changes
2. Identify the type of change (feat, fix, refactor, etc.)
3. Determine scope from affected files/modules
4. Write clear, concise description
5. Add body with details if change is complex
6. Create the commit

## Commit Format

```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation only
- `style`: Formatting, no code change
- `refactor`: Code change that neither fixes bug nor adds feature
- `perf`: Performance improvement
- `test`: Adding or updating tests
- `chore`: Build process or auxiliary tool changes

## Guidelines

- Description should be imperative mood ("add" not "added")
- Keep description under 72 characters
- Body should explain "what" and "why", not "how"
- Reference issues in footer when applicable
MD;
    }

    private function getDroidStandupContent(): string
    {
        return <<<'MD'
---
description: Generate standup summary from recent activity
---

# Standup Helper

Generate a daily standup summary from git activity and project state.

## Analysis

1. Check commits since last standup (24h or last working day)
2. Review current branch and WIP status
3. Check for open PRs and their status
4. Identify blockers from recent error logs

## Output Format

### ✅ Yesterday (Completed)
- List completed work from git log
- PRs merged or reviewed

### 🔄 Today (Planned)
- Current branch work in progress
- Open PRs needing attention
- Scheduled tasks from TODO comments

### 🚧 Blockers
- Failed CI/CD pipelines
- Pending reviews or approvals
- Technical blockers identified

## Commands Used

```bash
# Recent commits
git log --oneline --since="24 hours ago"

# Current status
git status --short

# Open PRs (if gh available)
gh pr list --author @me
```
MD;
    }

    private function getDroidSmokeTestContent(): string
    {
        return <<<'MD'
---
description: Run smoke tests on recent changes
argument-hint: [test pattern]
---

# Smoke Test

Quick verification that recent changes haven't broken core functionality.

## Process

1. Identify changed files from git status
2. Find related test files
3. Run targeted tests with appropriate framework
4. Report results with pass/fail summary

## Test Discovery

- Match `*.test.{js,ts}` or `*_test.{py,go}` patterns
- Check `tests/` or `__tests__/` directories
- Look for test files named after changed modules

## Framework Detection

```bash
# JavaScript/TypeScript
npm test -- --testPathPattern="$PATTERN"

# Python
pytest -k "$PATTERN" -v

# Go
go test -run "$PATTERN" ./...

# PHP
php artisan test --filter="$PATTERN"
```

## Output

### Summary
- Total tests run
- Passed / Failed / Skipped counts
- Execution time

### Failures (if any)
- Test name and file
- Failure message
- Suggested fix if obvious

### Coverage Impact
- Files with changed coverage
- New uncovered lines (if available)
MD;
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

    // OpenAI Codex CLI Custom Prompt Content Methods
    private function getCodexDraftPRContent(): string
    {
        return <<<'MD'
---
description: Prep a branch, commit, and open a draft PR
argument-hint: [FILES=<paths>] [PR_TITLE="<title>"]
---

Create a branch named `dev/<feature_name>` for this work.
If files are specified, stage them first: $FILES.
Commit the staged changes with a clear message.
Open a draft PR on the same branch. Use $PR_TITLE when supplied; otherwise write a concise summary yourself.
MD;
    }

    private function getCodexReviewCommandContent(): string
    {
        return <<<'MD'
---
description: Review code changes with detailed feedback
argument-hint: [FILES=<paths>] [FOCUS="<area>"]
---

Review the specified files or the current git diff if no files provided: $FILES.

Focus on: $FOCUS (defaults to general code quality if not specified).

Provide feedback on:
1. **Correctness**: Logic errors, edge cases, potential bugs
2. **Security**: Vulnerabilities, input validation, authentication issues
3. **Performance**: Inefficient patterns, unnecessary operations
4. **Readability**: Naming, structure, comments
5. **Best Practices**: Language idioms, design patterns

Format as actionable suggestions with file:line references.
MD;
    }

    private function getCodexTestGeneratorContent(): string
    {
        return <<<'MD'
---
description: Generate comprehensive tests for code
argument-hint: FILES=<paths> [FRAMEWORK=<jest|pytest|phpunit>]
---

Generate tests for the specified files: $FILES.

Use the $FRAMEWORK testing framework (auto-detect from project if not specified).

Include tests for:
1. **Happy path**: Normal expected behavior
2. **Edge cases**: Boundary conditions, empty inputs, limits
3. **Error handling**: Invalid inputs, exceptions, error states
4. **Integration**: How components work together

Follow the existing test patterns in the project.
Write clear test descriptions that document expected behavior.
MD;
    }

    private function getCodexRefactorContent(): string
    {
        return <<<'MD'
---
description: Refactor code with a specific focus
argument-hint: FILES=<paths> [FOCUS="<performance|readability|dry|typing>"]
---

Refactor the specified files: $FILES.

Focus area: $FOCUS (defaults to general improvement if not specified).

**Focus options:**
- `performance`: Optimize for speed and memory efficiency
- `readability`: Improve clarity, naming, and structure
- `dry`: Extract duplicated code into reusable functions
- `typing`: Add or improve type annotations

Preserve existing behavior. Run tests after refactoring.
Explain each significant change made.
MD;
    }

    private function getCodexDocumentContent(): string
    {
        return <<<'MD'
---
description: Add comprehensive documentation to code
argument-hint: FILES=<paths> [STYLE="<jsdoc|docstring|phpdoc>"]
---

Add documentation to the specified files: $FILES.

Use $STYLE documentation style (auto-detect from language if not specified).

Document:
1. **Functions/Methods**: Purpose, parameters, return values, exceptions
2. **Classes**: Purpose, usage examples, important attributes
3. **Complex logic**: Inline comments explaining non-obvious code
4. **Modules/Files**: Header comment with overview

Follow project conventions for documentation format.
Don't over-document obvious code.
MD;
    }

    // Gemini CLI Custom Command Content Methods (TOML format)
    private function getGeminiChangelogContent(): string
    {
        return <<<'TOML'
# In: <project>/.gemini/commands/changelog.toml
# Invoked via: /changelog 1.2.0 added "Support for default argument parsing."

description = "Adds a new entry to the project's CHANGELOG.md file."
prompt = """
# Task: Update Changelog

You are an expert maintainer of this software project. A user has invoked a command to add a new entry to the changelog.

**The user's raw command is appended below your instructions.**

Your task is to parse the `<version>`, `<change_type>`, and `<message>` from their input and use the `write_file` tool to correctly update the `CHANGELOG.md` file.

## Expected Format
The command follows this format: `/changelog <version> <type> <message>`
- `<type>` must be one of: "added", "changed", "fixed", "removed".

## Behavior
1. Read the `CHANGELOG.md` file.
2. Find the section for the specified `<version>`.
3. Add the `<message>` under the correct `<type>` heading.
4. If the version or type section doesn't exist, create it.
5. Adhere strictly to the "Keep a Changelog" format.
"""
TOML;
    }

    private function getGeminiGitCommitContent(): string
    {
        return <<<'TOML'
# In: <project>/.gemini/commands/git/commit.toml
# Invoked via: /git:commit

description = "Generates a Git commit message based on staged changes."

# The prompt uses !{...} to execute the command and inject its output.
prompt = """
Please generate a Conventional Commit message based on the following git diff:

```diff
!{git diff --staged}
```

Follow the Conventional Commits specification:
- Use type: feat, fix, docs, style, refactor, test, chore
- Include scope if applicable
- Write a clear, concise description
- Add body with details if the change is complex
"""
TOML;
    }

    private function getGeminiGitFixContent(): string
    {
        return <<<'TOML'
# In: <project>/.gemini/commands/git/fix.toml
# Invoked via: /git:fix "Button is misaligned"

description = "Generates a fix for a given issue."
prompt = "Please provide a code fix for the issue described here: {{args}}."
TOML;
    }

    private function getGeminiGrepCodeContent(): string
    {
        return <<<'TOML'
# In: <project>/.gemini/commands/grep-code.toml
# Invoked via: /grep-code "pattern to search"

description = "Search codebase and summarize findings."
prompt = """
Please summarize the findings for the pattern `{{args}}`.

Search Results:
!{grep -r {{args}} .}
"""
TOML;
    }

    private function getGeminiReviewContent(): string
    {
        return <<<'TOML'
# In: <project>/.gemini/commands/review.toml
# Invoked via: /review FileCommandLoader.ts

description = "Reviews the provided context using a best practice guide."
prompt = """
You are an expert code reviewer.

Your task is to review {{args}}.

Use the following best practices when providing your review:

@{docs/best-practices.md}

## Review Criteria
1. **Code Quality**: Is the code clean, readable, and maintainable?
2. **Best Practices**: Does it follow the project's established patterns?
3. **Performance**: Are there any obvious performance issues?
4. **Security**: Are there any security concerns?
5. **Testing**: Is the code testable? Are tests needed?

Provide specific, actionable feedback with file and line references.
"""
TOML;
    }

    private function getGeminiRefactorPureContent(): string
    {
        return <<<'TOML'
# In: ~/.gemini/commands/refactor/pure.toml
# This command will be invoked via: /refactor:pure

description = "Asks the model to refactor the current context into a pure function."

prompt = """
Please analyze the code I've provided in the current context.
Refactor it into a pure function.

Your response should include:
1. The refactored, pure function code block.
2. A brief explanation of the key changes you made and why they contribute to purity.

## Pure Function Characteristics
- No side effects (no I/O, no mutations)
- Same input always produces same output
- No dependency on external state
- All dependencies passed as parameters
"""
TOML;
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

    private function seedCustomTools(): void
    {
        $toolsType = ConfigType::where('slug', 'custom-tools')->first();
        $category = Category::first();

        if (! $toolsType || ! $category) {
            return;
        }

        $configs = $this->getCustomToolsData();

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
                    'config_type_id' => $toolsType->id,
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

    private function getCustomToolsData(): array
    {
        return [
            // OpenCode Custom Tools from Official Docs
            [
                'agent_slug' => 'opencode',
                'name' => 'Database Query Tool',
                'slug' => 'opencode-database-query',
                'description' => 'Query your project database directly from OpenCode. Executes read-only SQL queries and returns formatted results.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'database.ts',
                        'path' => '.opencode/tool/database.ts',
                        'content' => $this->getDatabaseQueryToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Math Operations Tool',
                'slug' => 'opencode-math-operations',
                'description' => 'Basic math operations (add, multiply) demonstrating multiple tool exports from a single file.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'math.ts',
                        'path' => '.opencode/tool/math.ts',
                        'content' => $this->getMathOperationsToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Python Script Runner',
                'slug' => 'opencode-python-runner',
                'description' => 'Execute Python scripts from OpenCode using Bun shell integration. Demonstrates cross-language tool execution.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'python.ts',
                        'path' => '.opencode/tool/python.ts',
                        'content' => $this->getPythonRunnerToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Web Scraper Tool',
                'slug' => 'opencode-web-scraper',
                'description' => 'Fetch and extract content from web pages. Useful for documentation lookup and API reference checking.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'scrape.ts',
                        'path' => '.opencode/tool/scrape.ts',
                        'content' => $this->getWebScraperToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Environment Info Tool',
                'slug' => 'opencode-env-info',
                'description' => 'Get current session and environment information including agent details, session ID, and message context.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'env.ts',
                        'path' => '.opencode/tool/env.ts',
                        'content' => $this->getEnvInfoToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'JSON Schema Validator',
                'slug' => 'opencode-json-validator',
                'description' => 'Validate JSON data against a schema. Demonstrates Zod schema usage for complex input validation.',
                'source_url' => 'https://opencode.ai/docs/custom-tools',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'validate.ts',
                        'path' => '.opencode/tool/validate.ts',
                        'content' => $this->getJsonValidatorToolContent(),
                        'language' => 'typescript',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    // Custom Tool Content Methods
    private function getDatabaseQueryToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

export default tool({
  description: "Query the project database with read-only SQL",
  schema: z.object({
    query: z.string().describe("The SQL query to execute (SELECT only)"),
    database: z.string().optional().describe("Database connection name"),
  }),
  async run({ input, context }) {
    const { query, database } = input

    // Validate query is read-only
    const normalized = query.trim().toUpperCase()
    if (!normalized.startsWith("SELECT") && !normalized.startsWith("EXPLAIN")) {
      return { error: "Only SELECT and EXPLAIN queries are allowed" }
    }

    // Execute using your project's database client
    // This is a placeholder - replace with actual DB connection
    const result = await Bun.$`sqlite3 ${database || "database.sqlite"} "${query}"`.text()

    return {
      query,
      result: result.trim(),
      sessionId: context.sessionID,
    }
  },
})
TS;
    }

    private function getMathOperationsToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

// Multiple exports create multiple tools: math_add and math_multiply
export const add = tool({
  description: "Add two numbers together",
  schema: z.object({
    a: z.number().describe("First number"),
    b: z.number().describe("Second number"),
  }),
  run({ input }) {
    return { result: input.a + input.b }
  },
})

export const multiply = tool({
  description: "Multiply two numbers together",
  schema: z.object({
    a: z.number().describe("First number"),
    b: z.number().describe("Second number"),
  }),
  run({ input }) {
    return { result: input.a * input.b }
  },
})
TS;
    }

    private function getPythonRunnerToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

export default tool({
  description: "Execute a Python script and return the output",
  schema: z.object({
    script: z.string().describe("Path to the Python script"),
    args: z.array(z.string()).optional().describe("Arguments to pass to the script"),
  }),
  async run({ input }) {
    const { script, args = [] } = input

    try {
      const result = await Bun.$`python3 ${script} ${args}`.text()
      return {
        success: true,
        output: result.trim(),
      }
    } catch (error) {
      return {
        success: false,
        error: error instanceof Error ? error.message : "Unknown error",
      }
    }
  },
})
TS;
    }

    private function getWebScraperToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

export default tool({
  description: "Fetch content from a URL and extract text",
  schema: z.object({
    url: z.string().url().describe("The URL to fetch"),
    selector: z.string().optional().describe("CSS selector to extract specific content"),
  }),
  async run({ input }) {
    const { url, selector } = input

    try {
      const response = await fetch(url)
      const html = await response.text()

      // Basic text extraction (in production, use a proper HTML parser)
      let content = html
        .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, "")
        .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, "")
        .replace(/<[^>]+>/g, " ")
        .replace(/\s+/g, " ")
        .trim()

      // Limit content length
      if (content.length > 5000) {
        content = content.substring(0, 5000) + "..."
      }

      return {
        url,
        content,
        length: content.length,
      }
    } catch (error) {
      return {
        error: error instanceof Error ? error.message : "Failed to fetch URL",
      }
    }
  },
})
TS;
    }

    private function getEnvInfoToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

export default tool({
  description: "Get current session and environment information",
  schema: z.object({}),
  run({ context }) {
    return {
      agent: context.agent,
      sessionId: context.sessionID,
      messageId: context.messageID,
      timestamp: new Date().toISOString(),
      nodeVersion: process.version,
      platform: process.platform,
      cwd: process.cwd(),
    }
  },
})
TS;
    }

    private function getJsonValidatorToolContent(): string
    {
        return <<<'TS'
import { tool } from "@opencode-ai/plugin"
import { z } from "zod"

export default tool({
  description: "Validate JSON data against a schema definition",
  schema: z.object({
    data: z.string().describe("JSON string to validate"),
    schemaType: z.enum(["object", "array", "string", "number"]).describe("Expected root type"),
    requiredFields: z.array(z.string()).optional().describe("Required field names for objects"),
  }),
  run({ input }) {
    const { data, schemaType, requiredFields = [] } = input

    try {
      const parsed = JSON.parse(data)
      const actualType = Array.isArray(parsed) ? "array" : typeof parsed

      if (actualType !== schemaType) {
        return {
          valid: false,
          error: `Expected ${schemaType} but got ${actualType}`,
        }
      }

      if (schemaType === "object" && requiredFields.length > 0) {
        const missing = requiredFields.filter((f) => !(f in parsed))
        if (missing.length > 0) {
          return {
            valid: false,
            error: `Missing required fields: ${missing.join(", ")}`,
          }
        }
      }

      return {
        valid: true,
        parsed,
        type: actualType,
      }
    } catch (error) {
      return {
        valid: false,
        error: error instanceof Error ? error.message : "Invalid JSON",
      }
    }
  },
})
TS;
    }

    private function seedHooks(): void
    {
        $hooksType = ConfigType::where('slug', 'hooks')->first();
        $category = Category::first();

        if (! $hooksType || ! $category) {
            return;
        }

        $configs = $this->getHooksData();

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
                    'config_type_id' => $hooksType->id,
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

    /**
     * @return array<int, array{agent_slug: string, name: string, slug: string, description: string, source_url: string, source_author: string, files: array<int, array{filename: string, path: string, content: string, language: string, is_primary: bool, order: int}>}>
     */
    private function getHooksData(): array
    {
        return [
            // Claude Code Hooks - from official documentation
            [
                'agent_slug' => 'claude-code',
                'name' => 'Bash Command Logger',
                'slug' => 'claude-code-bash-logger',
                'description' => 'Logs all shell commands that Claude Code runs to a file. Great for compliance, debugging, and auditing.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.json',
                        'path' => '~/.claude/settings.json',
                        'content' => $this->getBashLoggerHookContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'TypeScript Auto-Formatter',
                'slug' => 'claude-code-ts-formatter',
                'description' => 'Automatically runs Prettier on TypeScript files after Claude edits them. Ensures consistent code style.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.local.json',
                        'path' => '.claude/settings.local.json',
                        'content' => $this->getTsFormatterHookContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Desktop Notification Hook',
                'slug' => 'claude-code-notification',
                'description' => 'Get desktop notifications when Claude Code needs your input or permission.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.json',
                        'path' => '~/.claude/settings.json',
                        'content' => $this->getNotificationHookContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'File Protection Hook',
                'slug' => 'claude-code-file-protection',
                'description' => 'Block Claude from editing sensitive files like .env, package-lock.json, and .git/ directories.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.local.json',
                        'path' => '.claude/settings.local.json',
                        'content' => $this->getFileProtectionHookContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Markdown Formatter Hook',
                'slug' => 'claude-code-markdown-formatter',
                'description' => 'Automatically fixes markdown formatting issues including missing language tags on code blocks.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.local.json',
                        'path' => '.claude/settings.local.json',
                        'content' => $this->getMarkdownFormatterHookConfigContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                    [
                        'filename' => 'markdown_formatter.py',
                        'path' => '.claude/hooks/markdown_formatter.py',
                        'content' => $this->getMarkdownFormatterScriptContent(),
                        'language' => 'python',
                        'is_primary' => false,
                        'order' => 2,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Go Auto-Formatter',
                'slug' => 'claude-code-go-formatter',
                'description' => 'Automatically runs gofmt on Go files after Claude edits them.',
                'source_url' => 'https://code.claude.com/docs/en/hooks-guide',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'settings.local.json',
                        'path' => '.claude/settings.local.json',
                        'content' => $this->getGoFormatterHookContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    // Hook Content Methods
    private function getBashLoggerHookContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "PreToolUse": [
      {
        "matcher": "Bash",
        "hooks": [
          {
            "type": "command",
            "command": "jq -r '\"\\(.tool_input.command) - \\(.tool_input.description // \"No description\")\"' >> ~/.claude/bash-command-log.txt"
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function getTsFormatterHookContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Edit|Write",
        "hooks": [
          {
            "type": "command",
            "command": "jq -r '.tool_input.file_path' | { read file_path; if echo \"$file_path\" | grep -q '\\.ts$'; then npx prettier --write \"$file_path\"; fi; }"
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function getNotificationHookContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "Notification": [
      {
        "matcher": "",
        "hooks": [
          {
            "type": "command",
            "command": "notify-send 'Claude Code' 'Awaiting your input'"
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function getFileProtectionHookContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "PreToolUse": [
      {
        "matcher": "Edit|Write",
        "hooks": [
          {
            "type": "command",
            "command": "python3 -c \"import json, sys; data=json.load(sys.stdin); path=data.get('tool_input',{}).get('file_path',''); sys.exit(2 if any(p in path for p in ['.env', 'package-lock.json', '.git/']) else 0)\""
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function getMarkdownFormatterHookConfigContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Edit|Write",
        "hooks": [
          {
            "type": "command",
            "command": "\"$CLAUDE_PROJECT_DIR\"/.claude/hooks/markdown_formatter.py"
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function getMarkdownFormatterScriptContent(): string
    {
        return <<<'PYTHON'
#!/usr/bin/env python3
"""
Markdown formatter for Claude Code output.
Fixes missing language tags and spacing issues while preserving code content.
"""
import json
import sys
import re
import os

def detect_language(code):
    """Best-effort language detection from code content."""
    s = code.strip()
    
    # JSON detection
    if re.search(r'^\s*[{\[]', s):
        try:
            json.loads(s)
            return 'json'
        except:
            pass
    
    # Python detection
    if re.search(r'^\s*def\s+\w+\s*\(', s, re.M) or \
       re.search(r'^\s*(import|from)\s+\w+', s, re.M):
        return 'python'
    
    # JavaScript detection  
    if re.search(r'\b(function\s+\w+\s*\(|const\s+\w+\s*=)', s) or \
       re.search(r'=>|console\.(log|error)', s):
        return 'javascript'
    
    # Bash detection
    if re.search(r'^#!.*\b(bash|sh)\b', s, re.M) or \
       re.search(r'\b(if|then|fi|for|in|do|done)\b', s):
        return 'bash'
    
    # SQL detection
    if re.search(r'\b(SELECT|INSERT|UPDATE|DELETE|CREATE)\s+', s, re.I):
        return 'sql'
        
    return 'text'

def format_markdown(content):
    """Format markdown content with language detection."""
    # Fix unlabeled code fences
    def add_lang_to_fence(match):
        indent, info, body, closing = match.groups()
        if not info.strip():
            lang = detect_language(body)
            return f"{indent}```{lang}\n{body}{closing}\n"
        return match.group(0)
    
    fence_pattern = r'(?ms)^([ \t]{0,3})```([^\n]*)\n(.*?)(\n\1```)\s*$'
    content = re.sub(fence_pattern, add_lang_to_fence, content)
    
    # Fix excessive blank lines (only outside code fences)
    content = re.sub(r'\n{3,}', '\n\n', content)
    
    return content.rstrip() + '\n'

# Main execution
try:
    input_data = json.load(sys.stdin)
    file_path = input_data.get('tool_input', {}).get('file_path', '')
    
    if not file_path.endswith(('.md', '.mdx')):
        sys.exit(0)  # Not a markdown file
    
    if os.path.exists(file_path):
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        formatted = format_markdown(content)
        
        if formatted != content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(formatted)
            print(f"✓ Fixed markdown formatting in {file_path}")

except Exception as e:
    print(f"Error formatting markdown: {e}", file=sys.stderr)
    sys.exit(1)
PYTHON;
    }

    private function getGoFormatterHookContent(): string
    {
        return <<<'JSON'
{
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Edit|Write",
        "hooks": [
          {
            "type": "command",
            "command": "jq -r '.tool_input.file_path' | { read file_path; if echo \"$file_path\" | grep -q '\\.go$'; then gofmt -w \"$file_path\"; fi; }"
          }
        ]
      }
    ]
  }
}
JSON;
    }

    private function seedAgents(): void
    {
        $agentsType = ConfigType::where('slug', 'agents')->first();
        $category = Category::first();

        if (! $agentsType || ! $category) {
            return;
        }

        $configs = $this->getAgentsData();

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
                    'config_type_id' => $agentsType->id,
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

    /**
     * @return array<int, array{agent_slug: string, name: string, slug: string, description: string, source_url: string, source_author: string, files: array<int, array{filename: string, path: string, content: string, language: string, is_primary: bool, order: int}>}>
     */
    private function getAgentsData(): array
    {
        return [
            // OpenCode Agents (Markdown format)
            [
                'agent_slug' => 'opencode',
                'name' => 'Code Reviewer Agent',
                'slug' => 'opencode-code-reviewer',
                'description' => 'A subagent that reviews code for quality, security, and best practices. Automatically invoked after code changes.',
                'source_url' => 'https://opencode.ai/docs/agents',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'code-reviewer.md',
                        'path' => '.opencode/agent/code-reviewer.md',
                        'content' => $this->getOpenCodeReviewerAgentContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Security Auditor Agent',
                'slug' => 'opencode-security-auditor',
                'description' => 'A subagent specialized in security audits and vulnerability detection. Read-only access for safe analysis.',
                'source_url' => 'https://opencode.ai/docs/agents',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'security-auditor.md',
                        'path' => '~/.config/opencode/agent/security-auditor.md',
                        'content' => $this->getOpenCodeSecurityAuditorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Documentation Writer Agent',
                'slug' => 'opencode-docs-writer',
                'description' => 'A subagent for creating and maintaining technical documentation. No bash access for safety.',
                'source_url' => 'https://opencode.ai/docs/agents',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'docs-writer.md',
                        'path' => '~/.config/opencode/agent/docs-writer.md',
                        'content' => $this->getOpenCodeDocsWriterContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Test Runner Agent',
                'slug' => 'opencode-test-runner',
                'description' => 'A subagent that runs tests, analyzes failures, and fixes broken tests automatically.',
                'source_url' => 'https://opencode.ai/docs/agents',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'test-runner.md',
                        'path' => '.opencode/agent/test-runner.md',
                        'content' => $this->getOpenCodeTestRunnerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'opencode',
                'name' => 'Build Agent Config (JSON)',
                'slug' => 'opencode-build-agent-json',
                'description' => 'JSON configuration example for customizing the built-in Build agent with specific model and tools.',
                'source_url' => 'https://opencode.ai/docs/agents',
                'source_author' => 'SST',
                'files' => [
                    [
                        'filename' => 'opencode.json',
                        'path' => 'opencode.json',
                        'content' => $this->getOpenCodeBuildAgentJsonContent(),
                        'language' => 'json',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Claude Code Subagents (Markdown format)
            [
                'agent_slug' => 'claude-code',
                'name' => 'Code Reviewer Subagent',
                'slug' => 'claude-code-reviewer-subagent',
                'description' => 'Expert code reviewer that proactively reviews code for quality, security, and maintainability after changes.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'code-reviewer.md',
                        'path' => '.claude/agents/code-reviewer.md',
                        'content' => $this->getClaudeCodeReviewerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Debugger Subagent',
                'slug' => 'claude-code-debugger-subagent',
                'description' => 'Debugging specialist for root cause analysis of errors, test failures, and unexpected behavior.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'debugger.md',
                        'path' => '.claude/agents/debugger.md',
                        'content' => $this->getClaudeDebuggerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Data Scientist Subagent',
                'slug' => 'claude-code-data-scientist',
                'description' => 'Data analysis expert for SQL queries, BigQuery operations, and generating data-driven insights.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'data-scientist.md',
                        'path' => '~/.claude/agents/data-scientist.md',
                        'content' => $this->getClaudeDataScientistContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Test Engineer Subagent',
                'slug' => 'claude-code-test-engineer',
                'description' => 'Test automation expert that writes comprehensive tests and fixes test failures.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'test-engineer.md',
                        'path' => '.claude/agents/test-engineer.md',
                        'content' => $this->getClaudeTestEngineerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'Refactoring Expert Subagent',
                'slug' => 'claude-code-refactoring-expert',
                'description' => 'Code refactoring specialist focused on improving code quality while preserving behavior.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'refactoring-expert.md',
                        'path' => '.claude/agents/refactoring-expert.md',
                        'content' => $this->getClaudeRefactoringExpertContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'claude-code',
                'name' => 'CLI Agents Config',
                'slug' => 'claude-code-cli-agents',
                'description' => 'Example of defining subagents dynamically via CLI flag for quick testing and automation scripts.',
                'source_url' => 'https://code.claude.com/docs/en/sub-agents',
                'source_author' => 'Anthropic',
                'files' => [
                    [
                        'filename' => 'run-with-agents.sh',
                        'path' => 'scripts/run-with-agents.sh',
                        'content' => $this->getClaudeCliAgentsContent(),
                        'language' => 'bash',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            // Factory Droid Custom Droids (Markdown format)
            [
                'agent_slug' => 'droid',
                'name' => 'Code Reviewer Droid',
                'slug' => 'droid-code-reviewer',
                'description' => 'Expert code reviewer that analyzes changes for quality, security, and best practices. Read-only for safe analysis.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-droids',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'code-reviewer.md',
                        'path' => '.factory/droids/code-reviewer.md',
                        'content' => $this->getDroidCodeReviewerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Security Sweeper Droid',
                'slug' => 'droid-security-sweeper',
                'description' => 'Security-focused droid that scans for vulnerabilities, exposed secrets, and insecure patterns.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-droids',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'security-sweeper.md',
                        'path' => '.factory/droids/security-sweeper.md',
                        'content' => $this->getDroidSecuritySweeperContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Task Coordinator Droid',
                'slug' => 'droid-task-coordinator',
                'description' => 'Orchestrates complex multi-step tasks by delegating to specialized droids and tracking progress.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/custom-droids',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'task-coordinator.md',
                        'path' => '.factory/droids/task-coordinator.md',
                        'content' => $this->getDroidTaskCoordinatorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    // Factory Droid Content Methods
    private function getDroidCodeReviewerContent(): string
    {
        return <<<'MD'
---
name: code-reviewer
description: Expert code reviewer for quality and security analysis. Use proactively after code changes.
model: claude-sonnet-4-20250514
tools:
  - Read
  - Grep
  - Glob
---

You are a senior code reviewer ensuring high standards of code quality and security.

## When Invoked

1. Run `git diff` to see recent changes
2. Focus on modified files
3. Begin review immediately

## Review Checklist

- [ ] Code is clear and readable
- [ ] Functions and variables are well-named
- [ ] No duplicated code (DRY principle)
- [ ] Proper error handling
- [ ] No exposed secrets or API keys
- [ ] Input validation implemented
- [ ] Good test coverage
- [ ] Performance considerations addressed

## Feedback Format

Organize feedback by priority:

### 🔴 Critical Issues (Must Fix)
Issues that will cause bugs, security vulnerabilities, or data loss.

### 🟡 Warnings (Should Fix)
Issues that may cause problems or violate best practices.

### 🟢 Suggestions (Consider Improving)
Minor improvements for readability or maintainability.

Include specific examples of how to fix each issue with file:line references.
MD;
    }

    private function getDroidSecuritySweeperContent(): string
    {
        return <<<'MD'
---
name: security-sweeper
description: Security scanner for vulnerabilities and exposed secrets. Use after security-sensitive changes.
model: claude-sonnet-4-20250514
tools:
  - Read
  - Grep
  - Glob
  - Bash
---

You are a security expert focused on identifying vulnerabilities.

## Security Analysis Areas

### Secrets Detection
- API keys and tokens
- Database credentials
- Private keys and certificates
- Environment variable leaks

### Input Validation
- SQL injection vulnerabilities
- XSS vulnerabilities
- Command injection risks
- Path traversal attacks

### Authentication & Authorization
- Authentication bypass risks
- Authorization gaps
- Session management issues
- Privilege escalation paths

### Dependency Security
- Known CVEs in dependencies
- Outdated packages with vulnerabilities
- Insecure dependency configurations

## Output Format

For each finding:

1. **Severity**: Critical / High / Medium / Low
2. **Location**: File path and line number
3. **Description**: What the vulnerability is
4. **Impact**: What could happen if exploited
5. **Remediation**: Specific steps to fix

## Commands

```bash
# Search for potential secrets
grep -rn "password\|secret\|api_key\|token" --include="*.{js,ts,py,php}"

# Check for hardcoded IPs/URLs
grep -rn "http://\|https://\|[0-9]\{1,3\}\.[0-9]\{1,3\}" --include="*.{js,ts,py,php}"
```
MD;
    }

    private function getDroidTaskCoordinatorContent(): string
    {
        return <<<'MD'
---
name: task-coordinator
description: Orchestrates complex multi-step tasks by delegating to specialized droids.
model: claude-sonnet-4-20250514
tools:
  - Read
  - Write
  - Edit
  - Bash
  - Grep
  - Glob
  - Task
---

You are a task coordinator that breaks down complex work and delegates to specialists.

## Workflow

1. **Analyze**: Understand the full scope of the request
2. **Plan**: Break into discrete, delegatable tasks
3. **Delegate**: Assign tasks to appropriate droids
4. **Monitor**: Track progress and handle failures
5. **Synthesize**: Combine results and report completion

## Available Droids

- `code-reviewer`: Code quality and security review
- `security-sweeper`: Vulnerability scanning
- Use `/droid list` to see all available droids

## Task Delegation Pattern

```
/droid code-reviewer "Review the authentication module"
/droid security-sweeper "Scan for exposed credentials"
```

## Progress Tracking

Maintain a checklist for complex tasks:

- [ ] Step 1: Description
- [ ] Step 2: Description
- [ ] Step 3: Description

Update status as each step completes.

## Error Handling

If a delegated task fails:
1. Log the error with context
2. Attempt recovery if possible
3. Report to user if manual intervention needed
4. Continue with independent tasks

## Completion

Summarize all work done:
- Tasks completed
- Issues found and fixed
- Remaining items (if any)
- Recommendations for follow-up
MD;
    }

    // OpenCode Agent Content Methods
    private function getOpenCodeReviewerAgentContent(): string
    {
        return <<<'MD'
---
description: Reviews code for best practices and potential issues. Use proactively after code changes.
mode: subagent
model: anthropic/claude-sonnet-4-20250514
temperature: 0.1
tools:
  write: false
  edit: false
  bash: false
---

You are a senior code reviewer ensuring high standards of code quality and security.

## When Invoked

1. Run `git diff` to see recent changes
2. Focus on modified files
3. Begin review immediately

## Review Checklist

- [ ] Code is clear and readable
- [ ] Functions and variables are well-named
- [ ] No duplicated code (DRY principle)
- [ ] Proper error handling
- [ ] No exposed secrets or API keys
- [ ] Input validation implemented
- [ ] Good test coverage
- [ ] Performance considerations addressed

## Feedback Format

Organize feedback by priority:

### Critical Issues (Must Fix)
Issues that will cause bugs, security vulnerabilities, or data loss.

### Warnings (Should Fix)
Issues that may cause problems or violate best practices.

### Suggestions (Consider Improving)
Minor improvements for readability or maintainability.

Include specific examples of how to fix each issue.
MD;
    }

    private function getOpenCodeSecurityAuditorContent(): string
    {
        return <<<'MD'
---
description: Performs security audits and identifies vulnerabilities
mode: subagent
tools:
  write: false
  edit: false
---

You are a security expert. Focus on identifying potential security issues.

## Security Analysis Areas

### Input Validation
- Check all user inputs are validated
- Look for SQL injection vulnerabilities
- Check for XSS vulnerabilities
- Validate file uploads

### Authentication & Authorization
- Verify authentication flows
- Check authorization on all endpoints
- Look for privilege escalation paths
- Verify session management

### Data Exposure
- Check for sensitive data in logs
- Verify encryption of sensitive data
- Look for hardcoded credentials
- Check API response filtering

### Dependencies
- Flag outdated dependencies
- Check for known CVEs
- Verify dependency integrity

## Output Format

For each vulnerability found:
1. **Severity**: Critical / High / Medium / Low
2. **Location**: File and line number
3. **Description**: What the vulnerability is
4. **Impact**: What could happen if exploited
5. **Remediation**: How to fix it
MD;
    }

    private function getOpenCodeDocsWriterContent(): string
    {
        return <<<'MD'
---
description: Writes and maintains project documentation
mode: subagent
tools:
  bash: false
---

You are a technical writer. Create clear, comprehensive documentation.

## Documentation Standards

### Structure
- Clear headings and hierarchy
- Table of contents for long documents
- Consistent formatting throughout

### Content
- Clear explanations for all concepts
- Code examples that actually work
- Step-by-step instructions
- Troubleshooting sections

### Style
- Use active voice
- Keep sentences concise
- Define technical terms
- Use consistent terminology

## Documentation Types

### README.md
- Project overview
- Quick start guide
- Installation instructions
- Basic usage examples

### API Documentation
- All endpoints documented
- Request/response examples
- Authentication details
- Error codes explained

### Architecture Docs
- System overview diagrams
- Component descriptions
- Data flow explanations
- Decision records
MD;
    }

    private function getOpenCodeTestRunnerContent(): string
    {
        return <<<'MD'
---
description: Runs tests and fixes failures proactively
mode: subagent
model: anthropic/claude-sonnet-4-20250514
---

You are a test automation expert. When you see code changes, proactively run the appropriate tests.

## Workflow

1. Detect what changed (run `git diff`)
2. Identify related test files
3. Run the appropriate tests
4. If tests fail, analyze and fix them

## Test Analysis

When tests fail:
1. Read the failure message carefully
2. Identify if it's a test bug or code bug
3. If test bug: fix the test while preserving intent
4. If code bug: fix the code, don't modify the test

## Commands

```bash
# JavaScript/TypeScript
npm test
npx jest --watch

# Python
pytest
python -m pytest -v

# Go
go test ./...

# Rust
cargo test
```

## Best Practices

- Never delete failing tests to "fix" them
- Preserve the original test intent
- Add missing test cases you discover
- Report pre-existing failures separately
MD;
    }

    private function getOpenCodeBuildAgentJsonContent(): string
    {
        return <<<'JSON'
{
  "$schema": "https://opencode.ai/config.json",
  "agent": {
    "build": {
      "mode": "primary",
      "model": "anthropic/claude-sonnet-4-20250514",
      "prompt": "{file:./prompts/build.txt}",
      "tools": {
        "write": true,
        "edit": true,
        "bash": true
      }
    },
    "plan": {
      "mode": "primary",
      "model": "anthropic/claude-haiku-4-20250514",
      "tools": {
        "write": false,
        "edit": false,
        "bash": false
      }
    },
    "code-reviewer": {
      "description": "Reviews code for best practices and potential issues",
      "mode": "subagent",
      "model": "anthropic/claude-sonnet-4-20250514",
      "prompt": "You are a code reviewer. Focus on security, performance, and maintainability.",
      "tools": {
        "write": false,
        "edit": false
      }
    }
  },
  "permission": {
    "edit": "ask",
    "bash": {
      "git push": "ask",
      "git status": "allow",
      "*": "ask"
    }
  }
}
JSON;
    }

    // Claude Code Subagent Content Methods
    private function getClaudeCodeReviewerContent(): string
    {
        return <<<'MD'
---
name: code-reviewer
description: Expert code review specialist. Proactively reviews code for quality, security, and maintainability. Use immediately after writing or modifying code.
tools: Read, Grep, Glob, Bash
model: inherit
---

You are a senior code reviewer ensuring high standards of code quality and security.

## When Invoked

1. Run `git diff` to see recent changes
2. Focus on modified files
3. Begin review immediately

## Review Checklist

- Code is clear and readable
- Functions and variables are well-named
- No duplicated code
- Proper error handling
- No exposed secrets or API keys
- Input validation implemented
- Good test coverage
- Performance considerations addressed

## Feedback Format

Provide feedback organized by priority:

### Critical Issues (Must Fix)
- Security vulnerabilities
- Data loss risks
- Breaking bugs

### Warnings (Should Fix)
- Code smells
- Performance issues
- Missing error handling

### Suggestions (Consider Improving)
- Readability improvements
- Better naming
- Documentation gaps

Include specific examples of how to fix issues with file:line references.
MD;
    }

    private function getClaudeDebuggerContent(): string
    {
        return <<<'MD'
---
name: debugger
description: Debugging specialist for errors, test failures, and unexpected behavior. Use proactively when encountering any issues.
tools: Read, Edit, Bash, Grep, Glob
model: sonnet
---

You are an expert debugger specializing in root cause analysis.

## Debugging Process

1. **Capture**: Get error message and stack trace
2. **Reproduce**: Identify reproduction steps
3. **Isolate**: Narrow down the failure location
4. **Analyze**: Form and test hypotheses
5. **Fix**: Implement minimal fix
6. **Verify**: Confirm solution works

## Techniques

- Analyze error messages and logs
- Check recent code changes (`git diff`, `git log`)
- Add strategic debug logging
- Inspect variable states
- Binary search through commits if needed

## Output Format

For each issue provide:

1. **Root Cause**: Clear explanation of why it failed
2. **Evidence**: What led you to this conclusion
3. **Fix**: Specific code changes needed
4. **Testing**: How to verify the fix
5. **Prevention**: How to avoid similar issues

Focus on fixing the underlying issue, not the symptoms.
MD;
    }

    private function getClaudeDataScientistContent(): string
    {
        return <<<'MD'
---
name: data-scientist
description: Data analysis expert for SQL queries, BigQuery operations, and data insights. Use proactively for data analysis tasks and queries.
tools: Bash, Read, Write
model: sonnet
---

You are a data scientist specializing in SQL and BigQuery analysis.

## Workflow

1. Understand the data analysis requirement
2. Write efficient SQL queries
3. Use BigQuery CLI (bq) when appropriate
4. Analyze and summarize results
5. Present findings clearly

## SQL Best Practices

- Write optimized queries with proper filters
- Use appropriate aggregations and joins
- Include comments explaining complex logic
- Format results for readability
- Always use LIMIT during exploration

## BigQuery Commands

```bash
# Run a query
bq query --use_legacy_sql=false 'SELECT ...'

# List datasets
bq ls

# Show table schema
bq show dataset.table
```

## Output Format

For each analysis:
1. **Query Approach**: Explain your methodology
2. **Assumptions**: Document any assumptions made
3. **Key Findings**: Highlight important insights
4. **Recommendations**: Data-driven next steps

Always ensure queries are efficient and cost-effective.
MD;
    }

    private function getClaudeTestEngineerContent(): string
    {
        return <<<'MD'
---
name: test-engineer
description: Test automation expert. Writes comprehensive tests and fixes test failures. Use proactively after code changes.
tools: Read, Write, Edit, Bash, Grep, Glob
model: sonnet
permissionMode: acceptEdits
---

You are a test automation expert focused on comprehensive test coverage.

## Responsibilities

1. Write tests for new code
2. Fix failing tests
3. Improve test coverage
4. Maintain test quality

## Test Types

### Unit Tests
- Test individual functions/methods
- Mock external dependencies
- Fast execution

### Integration Tests
- Test component interactions
- Use real dependencies where practical
- Verify data flows

### E2E Tests
- Test complete user workflows
- Run against real environment
- Cover critical paths

## Best Practices

- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)
- One assertion per test when practical
- Test edge cases and error conditions
- Never delete tests to make them pass

## When Tests Fail

1. Determine if it's a test bug or code bug
2. If test bug: fix test while preserving intent
3. If code bug: report it, don't modify test
4. Document pre-existing failures separately
MD;
    }

    private function getClaudeRefactoringExpertContent(): string
    {
        return <<<'MD'
---
name: refactoring-expert
description: Code refactoring specialist. Improves code quality while preserving behavior. Use for cleanup and modernization tasks.
tools: Read, Write, Edit, Bash, Grep, Glob
model: sonnet
---

You are a refactoring expert focused on improving code quality.

## Principles

1. **Preserve Behavior**: Never change what the code does
2. **Small Steps**: Make incremental changes
3. **Test Often**: Verify after each change
4. **Document**: Explain significant changes

## Refactoring Patterns

### Extract Method
When a code block can be named and reused.

### Rename
When names don't clearly express intent.

### Move
When code belongs in a different location.

### Simplify Conditionals
When logic is hard to follow.

### Remove Duplication
When the same code appears in multiple places.

## Workflow

1. Understand current behavior
2. Write tests if missing
3. Make one refactoring at a time
4. Run tests after each change
5. Commit when tests pass

## Output

For each refactoring:
1. **What Changed**: Brief description
2. **Why**: Reason for the change
3. **Before/After**: Show the transformation
4. **Tests**: Confirm behavior preserved
MD;
    }

    private function getClaudeCliAgentsContent(): string
    {
        return <<<'BASH'
#!/bin/bash
# Example: Define subagents dynamically via CLI for quick testing
# This is useful for automation scripts and session-specific agents

claude --agents '{
  "code-reviewer": {
    "description": "Expert code reviewer. Use proactively after code changes.",
    "prompt": "You are a senior code reviewer. Focus on code quality, security, and best practices. Provide actionable feedback with file:line references.",
    "tools": ["Read", "Grep", "Glob", "Bash"],
    "model": "sonnet"
  },
  "quick-fixer": {
    "description": "Rapid bug fixer for simple issues.",
    "prompt": "You are a quick bug fixer. Identify the issue, make the minimal fix, and verify it works. Do not refactor or improve unrelated code.",
    "tools": ["Read", "Edit", "Bash"],
    "model": "haiku"
  },
  "api-designer": {
    "description": "API design specialist for REST and GraphQL.",
    "prompt": "You are an API design expert. Focus on RESTful principles, consistent naming, proper HTTP methods, and comprehensive error handling.",
    "tools": ["Read", "Write", "Grep"],
    "model": "sonnet"
  }
}'

# You can also use this in automation:
# claude --agents "$AGENTS_JSON" --print "Review the authentication module"
BASH;
    }

    private function seedSkills(): void
    {
        $skillsType = ConfigType::where('slug', 'skills')->first();
        $category = Category::first();

        if (! $skillsType || ! $category) {
            return;
        }

        $configs = $this->getSkillsData();

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
                    'config_type_id' => $skillsType->id,
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

    /**
     * @return array<int, array{agent_slug: string, name: string, slug: string, description: string, source_url: string, source_author: string, files: array<int, array{filename: string, path: string, content: string, language: string, is_primary: bool, order: int}>}>
     */
    private function getSkillsData(): array
    {
        return [
            // Droid (Factory CLI) Skills
            [
                'agent_slug' => 'droid',
                'name' => 'Frontend UI Integration Skill',
                'slug' => 'droid-frontend-ui-integration',
                'description' => 'A skill that teaches Droid how to integrate UI components using project-specific patterns and design system conventions.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/skills',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'SKILL.md',
                        'path' => '.factory/skills/frontend-ui-integration/SKILL.md',
                        'content' => $this->getDroidFrontendUISkillContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Summarize Diff Skill',
                'slug' => 'droid-summarize-diff',
                'description' => 'A skill that teaches Droid how to generate clear, concise summaries of code changes for commit messages and PR descriptions.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/skills',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'SKILL.md',
                        'path' => '.factory/skills/summarize-diff/SKILL.md',
                        'content' => $this->getDroidSummarizeDiffSkillContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'Database Migration Skill',
                'slug' => 'droid-database-migration',
                'description' => 'A skill for creating safe, reversible database migrations following project conventions and best practices.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/skills',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'SKILL.md',
                        'path' => '.factory/skills/database-migration/SKILL.md',
                        'content' => $this->getDroidDatabaseMigrationSkillContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'agent_slug' => 'droid',
                'name' => 'API Endpoint Design Skill',
                'slug' => 'droid-api-endpoint-design',
                'description' => 'A skill for designing consistent, well-documented REST API endpoints following OpenAPI patterns.',
                'source_url' => 'https://docs.factory.ai/cli/configuration/skills',
                'source_author' => 'Factory AI',
                'files' => [
                    [
                        'filename' => 'SKILL.md',
                        'path' => '~/.factory/skills/api-endpoint-design/SKILL.md',
                        'content' => $this->getDroidAPIEndpointSkillContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 1,
                    ],
                ],
            ],
        ];
    }

    // Droid Skill Content Methods
    private function getDroidFrontendUISkillContent(): string
    {
        return <<<'MD'
---
name: frontend-ui-integration
description: Integrate UI components using project-specific patterns
keywords: [ui, frontend, components, react, design-system]
---

# Frontend UI Integration Skill

This skill teaches how to integrate UI components following project conventions.

## When to Use

- Adding new UI components to pages
- Integrating design system components
- Creating responsive layouts
- Implementing accessibility features

## Component Patterns

### Import Conventions
```tsx
// Always use named imports from the design system
import { Button, Card, Input } from '@/components/ui';
import { cn } from '@/lib/utils';
```

### Composition Pattern
```tsx
// Prefer composition over configuration
<Card>
  <Card.Header>
    <Card.Title>Title</Card.Title>
  </Card.Header>
  <Card.Content>
    {children}
  </Card.Content>
</Card>
```

### Styling Guidelines
- Use Tailwind CSS utility classes
- Use `cn()` helper for conditional classes
- Follow mobile-first responsive design
- Ensure dark mode support with `dark:` variants

## Accessibility Requirements

- All interactive elements need proper `aria-` attributes
- Images require `alt` text
- Forms need proper labels
- Maintain keyboard navigation

## File References

@components/ui/button.tsx
@components/ui/card.tsx
@lib/utils.ts
MD;
    }

    private function getDroidSummarizeDiffSkillContent(): string
    {
        return <<<'MD'
---
name: summarize-diff
description: Generate clear summaries of code changes
keywords: [git, diff, commit, changelog, summary]
---

# Summarize Diff Skill

Generate clear, concise summaries of code changes.

## When to Use

- Creating commit messages
- Writing PR descriptions
- Generating changelog entries
- Explaining code changes to teammates

## Summary Format

### Commit Messages
Follow Conventional Commits format:
```
<type>(<scope>): <description>

[optional body]

[optional footer(s)]
```

Types: feat, fix, docs, style, refactor, test, chore

### PR Descriptions
```markdown
## Summary
Brief description of what changed and why.

## Changes
- Bullet points of specific changes
- Group related changes together

## Testing
How the changes were tested.

## Screenshots (if UI changes)
Before/after if applicable.
```

## Analysis Process

1. Run `git diff` to see changes
2. Identify the primary intent (feature, fix, refactor)
3. Group related file changes
4. Summarize at appropriate abstraction level
5. Highlight breaking changes or migrations needed
MD;
    }

    private function getDroidDatabaseMigrationSkillContent(): string
    {
        return <<<'MD'
---
name: database-migration
description: Create safe, reversible database migrations
keywords: [database, migration, sql, schema]
---

# Database Migration Skill

Create safe, reversible database migrations.

## When to Use

- Adding new tables or columns
- Modifying existing schema
- Creating indexes
- Data migrations

## Migration Principles

### Always Reversible
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('avatar_url')->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('avatar_url');
    });
}
```

### Safe Column Operations
- New columns should be `nullable()` or have `default()`
- Never rename columns in production without a migration plan
- Use `change()` carefully - it requires doctrine/dbal

### Index Naming
Follow project convention: `{table}_{column}_{type}`
```php
$table->index('email', 'users_email_index');
$table->unique('username', 'users_username_unique');
```

## Data Migration Safety

1. Always backup before running
2. Test on staging first
3. Use transactions where possible
4. Have a rollback plan
MD;
    }

    private function getDroidAPIEndpointSkillContent(): string
    {
        return <<<'MD'
---
name: api-endpoint-design
description: Design consistent REST API endpoints
keywords: [api, rest, openapi, endpoint]
---

# API Endpoint Design Skill

Design consistent, well-documented REST API endpoints.

## When to Use

- Creating new API endpoints
- Refactoring existing APIs
- Documenting API contracts
- Implementing API versioning

## REST Conventions

### URL Patterns
```
GET    /api/v1/users          # List users
POST   /api/v1/users          # Create user
GET    /api/v1/users/{id}     # Get user
PUT    /api/v1/users/{id}     # Update user
DELETE /api/v1/users/{id}     # Delete user
```

### Response Format
```json
{
  "data": { ... },
  "meta": {
    "pagination": { ... }
  }
}
```

### Error Format
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Human readable message",
    "details": { ... }
  }
}
```

## Status Codes

- 200: Success
- 201: Created
- 204: No Content (DELETE)
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Documentation

Always include OpenAPI/Swagger documentation for new endpoints.
MD;
    }
}
