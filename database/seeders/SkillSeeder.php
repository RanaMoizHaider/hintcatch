<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $systemUser = User::where('username', 'ranamoizhaider')->first();

        $utilitiesCategory = Category::where('slug', 'utilities')->first();
        $productivityCategory = Category::where('slug', 'productivity')->first();
        $developerCategory = Category::where('slug', 'developer-tools')->first();

        $skills = [
            [
                'name' => 'Code Review',
                'slug' => 'code-review',
                'description' => 'Comprehensive code review skill for identifying bugs, security issues, and suggesting improvements',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => '',
                    'version' => '1.0.0',
                    'tags' => ['review', 'quality', 'best-practices'],
                ],
                'allowed_tools' => ['read', 'grep', 'glob', 'lsp_diagnostics'],
                'github_url' => null,
                'category_id' => $utilitiesCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getCodeReviewContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Git Commit',
                'slug' => 'git-commit',
                'description' => 'Skill for creating well-structured, conventional git commits with proper messages',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => '',
                    'version' => '1.0.0',
                    'tags' => ['git', 'commits', 'conventional'],
                ],
                'allowed_tools' => ['bash', 'read'],
                'github_url' => null,
                'category_id' => $utilitiesCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getGitCommitContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                    [
                        'filename' => 'validate-commit.sh',
                        'path' => 'scripts/',
                        'content' => "#!/bin/bash\n# Validates commit message format\ncommit_msg=\"\$1\"\npattern='^(feat|fix|docs|style|refactor|test|chore)(\\(.+\\))?: .{1,50}'",
                        'language' => 'bash',
                        'is_primary' => false,
                        'order' => 1,
                    ],
                    [
                        'filename' => 'conventional-commits.md',
                        'path' => 'references/',
                        'content' => "# Conventional Commits\n\nSee https://www.conventionalcommits.org/",
                        'language' => 'markdown',
                        'is_primary' => false,
                        'order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Testing Guide',
                'slug' => 'testing-guide',
                'description' => 'Comprehensive testing skill with templates, fixtures, and helpers for unit, integration, and e2e tests',
                'license' => 'Apache-2.0',
                
                'metadata' => [
                    'author' => '',
                    'version' => '1.0.0',
                    'tags' => ['testing', 'unit-tests', 'integration', 'e2e'],
                ],
                'allowed_tools' => ['read', 'write', 'bash', 'glob'],
                'github_url' => null,
                'category_id' => $utilitiesCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTestingGuideContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                    [
                        'filename' => 'run-tests.sh',
                        'path' => 'scripts/',
                        'content' => "#!/bin/bash\nset -e\nnpm test -- --coverage",
                        'language' => 'bash',
                        'is_primary' => false,
                        'order' => 1,
                    ],
                    [
                        'filename' => 'setup-fixtures.sh',
                        'path' => 'scripts/',
                        'content' => "#!/bin/bash\nmkdir -p tests/fixtures\necho 'Fixtures directory ready'",
                        'language' => 'bash',
                        'is_primary' => false,
                        'order' => 2,
                    ],
                    [
                        'filename' => 'test-templates.md',
                        'path' => 'references/',
                        'content' => $this->getTestTemplatesContent(),
                        'language' => 'markdown',
                        'is_primary' => false,
                        'order' => 3,
                    ],
                    [
                        'filename' => 'test-fixtures.md',
                        'path' => 'references/',
                        'content' => $this->getTestFixturesContent(),
                        'language' => 'markdown',
                        'is_primary' => false,
                        'order' => 4,
                    ],
                    [
                        'filename' => 'test-helpers.md',
                        'path' => 'references/',
                        'content' => $this->getTestHelpersContent(),
                        'language' => 'markdown',
                        'is_primary' => false,
                        'order' => 5,
                    ],
                    [
                        'filename' => 'sample-data.json',
                        'path' => 'assets/',
                        'content' => json_encode(['users' => [['id' => 1, 'name' => 'Test User']]]),
                        'language' => 'json',
                        'is_primary' => false,
                        'order' => 6,
                    ],
                ],
            ],
            // Productivity Skills from awesome-claude-code
            [
                'name' => 'Context Management',
                'slug' => 'context-management',
                'description' => 'Advanced context management for optimal AI performance with file filtering and prioritization',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'OpenCode Community',
                    'version' => '1.0.0',
                    'tags' => ['context', 'performance', 'ai-optimization'],
                ],
                'allowed_tools' => ['read', 'write', 'glob', 'mgrep'],
                'github_url' => 'https://github.com/hesreallyhim/awesome-claude-code',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getContextManagementContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                    [
                        'filename' => 'context-analyze.js',
                        'path' => 'scripts/',
                        'content' => "// Analyze current context usage\nconst analyzeContext = () => {\n  return {\n    totalFiles: context.files.length,\n    contextSize: JSON.stringify(context).length,\n    recommendations: generateRecommendations()\n  };\n};",
                        'language' => 'javascript',
                        'is_primary' => false,
                        'order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Project Templates',
                'slug' => 'project-templates',
                'description' => 'Comprehensive project templates for various frameworks and languages',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'OpenCode Community',
                    'version' => '1.0.0',
                    'tags' => ['templates', 'boilerplate', 'project-setup'],
                ],
                'allowed_tools' => ['write', 'bash', 'glob'],
                'github_url' => 'https://github.com/hesreallyhim/awesome-claude-code',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getProjectTemplatesContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                    [
                        'filename' => 'laravel-starter.zip',
                        'path' => 'assets/',
                        'content' => 'base64-encoded-laravel-template',
                        'language' => 'binary',
                        'is_primary' => false,
                        'order' => 1,
                    ],
                    [
                        'filename' => 'react-starter.zip',
                        'path' => 'assets/',
                        'content' => 'base64-encoded-react-template',
                        'language' => 'binary',
                        'is_primary' => false,
                        'order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Status Line Integration',
                'slug' => 'status-line',
                'description' => 'Custom status line components for IDE integration',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'OpenCode Community',
                    'version' => '1.0.0',
                    'tags' => ['status-line', 'ide', 'ui'],
                ],
                'allowed_tools' => ['read', 'write'],
                'github_url' => 'https://github.com/hesreallyhim/awesome-claude-code',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getStatusLineContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Task Automation',
                'slug' => 'task-automation',
                'description' => 'Automated task management and workflow optimization',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'OpenCode Community',
                    'version' => '1.0.0',
                    'tags' => ['automation', 'tasks', 'workflow'],
                ],
                'allowed_tools' => ['bash', 'write', 'read'],
                'github_url' => 'https://github.com/hesreallyhim/awesome-claude-code',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTaskAutomationContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            // Agentic Subagents (OpenCode) - https://github.com/Cluster444/agentic
            [
                'name' => 'Codebase Analyzer',
                'slug' => 'agentic-codebase-analyzer',
                'description' => 'Analyzes codebase structure, patterns, and architecture to provide comprehensive understanding of the project',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['analysis', 'codebase', 'architecture', 'agentic'],
                ],
                'allowed_tools' => ['read', 'glob', 'grep', 'mgrep'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticCodebaseAnalyzerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Codebase Locator',
                'slug' => 'agentic-codebase-locator',
                'description' => 'Locates specific code elements, functions, classes, and patterns within the codebase',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['search', 'locate', 'codebase', 'agentic'],
                ],
                'allowed_tools' => ['read', 'glob', 'grep', 'mgrep', 'lsp_goto_definition', 'lsp_find_references'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticCodebaseLocatorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Codebase Pattern Finder',
                'slug' => 'agentic-codebase-pattern-finder',
                'description' => 'Identifies and documents recurring patterns, conventions, and anti-patterns in the codebase',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['patterns', 'conventions', 'analysis', 'agentic'],
                ],
                'allowed_tools' => ['read', 'glob', 'grep', 'mgrep', 'ast_grep_search'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticPatternFinderContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Thoughts Analyzer',
                'slug' => 'agentic-thoughts-analyzer',
                'description' => 'Analyzes reasoning and thought processes to improve decision-making and problem-solving',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['reasoning', 'analysis', 'thinking', 'agentic'],
                ],
                'allowed_tools' => ['read'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticThoughtsAnalyzerContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Thoughts Locator',
                'slug' => 'agentic-thoughts-locator',
                'description' => 'Locates and retrieves relevant thoughts, decisions, and reasoning from previous interactions',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['memory', 'context', 'thinking', 'agentic'],
                ],
                'allowed_tools' => ['read', 'grep'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticThoughtsLocatorContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Web Search Researcher',
                'slug' => 'agentic-web-search-researcher',
                'description' => 'Performs web research to gather information, documentation, and solutions for development tasks',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Chris Covington',
                    'version' => '1.0.0',
                    'tags' => ['research', 'web-search', 'documentation', 'agentic'],
                ],
                'allowed_tools' => ['webfetch', 'websearch_exa_web_search_exa'],
                'github_url' => 'https://github.com/Cluster444/agentic',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getAgenticWebSearchContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            // TÂCHES Skills (Claude Code) - https://github.com/glittercowboy/taches-cc-resources
            [
                'name' => 'Create Agent Skills',
                'slug' => 'taches-create-agent-skills',
                'description' => 'Skill for creating new agent skills with proper structure, documentation, and best practices',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['meta', 'skills', 'creation', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'glob'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreateAgentSkillsContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Create Hooks',
                'slug' => 'taches-create-hooks',
                'description' => 'Skill for creating lifecycle hooks that trigger on specific events during agent execution',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['hooks', 'lifecycle', 'automation', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'glob'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreateHooksContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Create Meta Prompts',
                'slug' => 'taches-create-meta-prompts',
                'description' => 'Skill for creating meta-prompts that generate other prompts based on patterns and templates',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['meta-prompts', 'templates', 'generation', 'taches'],
                ],
                'allowed_tools' => ['read', 'write'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreateMetaPromptsContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Create Plans',
                'slug' => 'taches-create-plans',
                'description' => 'Skill for creating structured execution plans with tasks, dependencies, and milestones',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['planning', 'tasks', 'project-management', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'todowrite'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $productivityCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreatePlansContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Create Slash Commands',
                'slug' => 'taches-create-slash-commands',
                'description' => 'Skill for creating custom slash commands with proper frontmatter and documentation',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['commands', 'slash-commands', 'automation', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'glob'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreateSlashCommandsContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Create Subagents',
                'slug' => 'taches-create-subagents',
                'description' => 'Skill for creating specialized subagents with defined capabilities and boundaries',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['subagents', 'delegation', 'specialization', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'glob'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesCreateSubagentsContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Debug Like Expert',
                'slug' => 'taches-debug-like-expert',
                'description' => 'Expert debugging skill with systematic approach to identifying and fixing issues',
                'license' => 'MIT',
                
                'metadata' => [
                    'author' => 'Lex Christopherson',
                    'version' => '1.0.0',
                    'tags' => ['debugging', 'troubleshooting', 'expert', 'taches'],
                ],
                'allowed_tools' => ['read', 'write', 'bash', 'grep', 'lsp_diagnostics'],
                'github_url' => 'https://github.com/glittercowboy/taches-cc-resources',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getTachesDebugLikeExpertContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            // Claude Codex Settings Skills - https://github.com/fcakyon/claude-codex-settings
            [
                'name' => 'Playwright Testing',
                'slug' => 'codex-playwright-testing',
                'description' => 'Comprehensive Playwright testing skill for browser automation and E2E testing',
                'license' => 'Apache-2.0',
                
                'metadata' => [
                    'author' => 'Fatih Akyon',
                    'version' => '1.0.0',
                    'tags' => ['testing', 'playwright', 'e2e', 'browser-automation'],
                ],
                'allowed_tools' => ['read', 'write', 'bash', 'playwriter_execute'],
                'github_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getCodexPlaywrightTestingContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
            [
                'name' => 'Plugin Development',
                'slug' => 'codex-plugin-development',
                'description' => 'Skill for developing Claude Code and Codex plugins with proper structure and best practices',
                'license' => 'Apache-2.0',
                
                'metadata' => [
                    'author' => 'Fatih Akyon',
                    'version' => '1.0.0',
                    'tags' => ['plugins', 'development', 'extensions'],
                ],
                'allowed_tools' => ['read', 'write', 'bash', 'glob'],
                'github_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'category_id' => $developerCategory?->id,
                'is_featured' => false,
                'files' => [
                    [
                        'filename' => 'skill.md',
                        'content' => $this->getCodexPluginDevelopmentContent(),
                        'language' => 'markdown',
                        'is_primary' => true,
                        'order' => 0,
                    ],
                ],
            ],
        ];

        foreach ($skills as $skillData) {
            $files = $skillData['files'] ?? [];
            unset($skillData['files']);

            $primaryFile = collect($files)->firstWhere('is_primary', true);
            $skillData['content'] = $primaryFile['content'] ?? '';

            $skill = Skill::updateOrCreate(
                ['slug' => $skillData['slug']],
                [...$skillData, 'submitted_by' => $systemUser?->id]
            );

            $skill->files()->delete();

            foreach ($files as $fileData) {
                $skill->files()->create($fileData);
            }
        }
    }

    private function getCodeReviewContent(): string
    {
        return <<<'MD'
# Code Review Skill

When reviewing code, follow these guidelines:

## Process

1. **Understand Context**: Read the PR description and related issues
2. **Check Architecture**: Verify the changes fit the overall design
3. **Review Logic**: Look for bugs, edge cases, and error handling
4. **Security Scan**: Check for vulnerabilities and data exposure
5. **Performance**: Identify potential bottlenecks
6. **Readability**: Ensure code is clear and well-documented

## Review Checklist

- [ ] Code follows project conventions
- [ ] No hardcoded secrets or credentials
- [ ] Error handling is comprehensive
- [ ] Tests cover new functionality
- [ ] No unnecessary complexity
- [ ] Dependencies are justified

## Feedback Format

Provide feedback as:
- **Critical**: Must fix before merge
- **Suggestion**: Recommended improvement
- **Nitpick**: Minor style preference
MD;
    }

    private function getGitCommitContent(): string
    {
        return <<<'MD'
# Git Commit Skill

Create well-structured commits following conventional commit format.

## Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

## Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation only
- **style**: Formatting, no code change
- **refactor**: Code change without feature/fix
- **test**: Adding tests
- **chore**: Maintenance tasks

## Guidelines

1. Subject line max 50 characters
2. Body wrapped at 72 characters
3. Use imperative mood ("Add" not "Added")
4. Reference issues in footer

## Examples

```
feat(auth): add OAuth2 support

Implement OAuth2 authentication flow with Google and GitHub providers.
Includes token refresh and session management.

Closes #123
```
MD;
    }

    private function getTestingGuideContent(): string
    {
        return <<<'MD'
# Testing Guide Skill

Comprehensive testing approach for all test types.

## Test Philosophy

- Test behavior, not implementation
- One assertion per test when possible
- Use descriptive test names
- Arrange-Act-Assert pattern

## Test Types

### Unit Tests
Test individual functions/methods in isolation.

### Integration Tests
Test component interactions and data flow.

### E2E Tests
Test complete user flows through the application.

## When to Use Each

| Scenario | Test Type |
|----------|-----------|
| Pure function | Unit |
| API endpoint | Integration |
| User signup flow | E2E |
| Database query | Integration |
| React component | Unit + Integration |
MD;
    }

    private function getTestTemplatesContent(): string
    {
        return <<<'MD'
# Test Templates

## Unit Test Template

```javascript
describe('FunctionName', () => {
  it('should return expected result for valid input', () => {
    const result = functionName(validInput);
    expect(result).toBe(expectedOutput);
  });

  it('should throw error for invalid input', () => {
    expect(() => functionName(invalidInput)).toThrow();
  });
});
```

## Integration Test Template

```javascript
describe('API Endpoint', () => {
  beforeEach(async () => {
    await setupDatabase();
  });

  it('should return data for authenticated user', async () => {
    const response = await request(app)
      .get('/api/resource')
      .set('Authorization', `Bearer ${token}`);
    
    expect(response.status).toBe(200);
  });
});
```
MD;
    }

    private function getTestFixturesContent(): string
    {
        return <<<'MD'
# Test Fixtures Guide

## Fixture Organization

```
tests/
  fixtures/
    users.json
    products.json
    orders.json
  factories/
    userFactory.js
    productFactory.js
```

## Loading Fixtures

```javascript
const fixtures = require('./fixtures/users.json');

beforeEach(() => {
  await db.users.insertMany(fixtures);
});
```
MD;
    }

    private function getTestHelpersContent(): string
    {
        return <<<'MD'
# Test Helpers

## Common Helpers

```javascript
// Wait for async operations
const waitFor = (ms) => new Promise(r => setTimeout(r, ms));

// Create authenticated request
const authRequest = (app, token) => 
  request(app).set('Authorization', `Bearer ${token}`);

// Reset database state
const resetDb = async () => {
  await db.dropDatabase();
  await db.migrate();
};
```
MD;
    }

    private function getContextManagementContent(): string
    {
        return <<<'MD'
# Context Management Skill

Optimize AI context for better performance and relevance.

## Context Strategy

### File Prioritization
- Core application files first
- Configuration files next
- Documentation last
- Exclude dependencies and build artifacts

### Content Filtering
- Remove duplicate imports
- Consolidate similar functions
- Focus on business logic over utilities

### Context Size Management
- Keep under 100K tokens for optimal performance
- Use file summaries for large modules
- Prioritize recently modified files

## Implementation

```javascript
const contextOptimizer = {
  // Filter relevant files
  filterFiles: (files, query) => {
    return files
      .filter(f => !f.path.includes('node_modules'))
      .filter(f => !f.path.includes('.git'))
      .sort((a, b) => b.modified - a.modified);
  },
  
  // Generate file summaries
  summarizeFile: (content) => {
    return `File: ${content.name}\\nPurpose: ${extractPurpose(content)}\\nKey functions: ${extractFunctions(content).slice(0, 5)}`;
  }
};
```
MD;
    }

    private function getProjectTemplatesContent(): string
    {
        return <<<'MD'
# Project Templates Skill

Ready-to-use project templates for various frameworks.

## Available Templates

### Laravel Starter
- Authentication system
- User management
- API endpoints
- Testing setup

### React Application
- Component structure
- State management
- Routing
- Build configuration

### Node.js API
- Express server
- Database connection
- Middleware setup
- Error handling

### Vue.js SPA
- Component composition
- State management
- Router configuration
- Build tools

## Usage

```bash
# Create new project from template
claude "Create a Laravel project with authentication"

# Use specific template
claude "Use React template with TypeScript"
```

## Template Features

- Pre-configured build tools
- Best practice folder structure
- Development environment setup
- CI/CD pipeline templates
MD;
    }

    private function getStatusLineContent(): string
    {
        return <<<'MD'
# Status Line Integration Skill

Custom status line components for better development experience.

## Status Components

### Git Status
- Current branch
- Uncommitted changes
- Sync status with remote

### Project Info
- Framework version
- Environment (dev/prod)
- Database status

### AI Assistant
- Active model
- Token usage
- Response time

### Performance Metrics
- Memory usage
- CPU usage
- Build time

## Installation

```json
{
  "statusline": {
    "components": [
      "git-status",
      "project-info", 
      "ai-assistant",
      "performance"
    ],
    "updateInterval": 5000
  }
}
```

## Customization

Add custom components by implementing the status interface:

```javascript
const customStatus = {
  name: "weather",
  update: async () => {
    const weather = await getWeather();
    return `🌤 ${weather.temp}°C`;
  }
};
```
MD;
    }

    private function getTaskAutomationContent(): string
    {
        return <<<'MD'
# Task Automation Skill

Automated task management and workflow optimization.

## Automation Rules

### Code Generation Tasks
- Generate boilerplate code
- Create test templates
- Setup project structure
- Generate documentation

### Development Tasks
- Run linting and formatting
- Execute test suites
- Build and deploy
- Database migrations

### Maintenance Tasks
- Update dependencies
- Clean cache and logs
- Backup databases
- Security audits

## Workflow Templates

### New Feature Development
```yaml
workflow:
  - name: "Create feature branch"
    command: "git checkout -b feature/{{feature_name}}"
  
  - name: "Generate boilerplate"
    template: "feature-template"
    
  - name: "Run tests"
    command: "npm test"
    
  - name: "Create PR"
    command: "gh pr create"
```

### Bug Fix Workflow
```yaml
workflow:
  - name: "Create bugfix branch"
    command: "git checkout -b fix/{{bug_id}}"
    
  - name: "Analyze issue"
    ai: "analyze_github_issue"
    
  - name: "Implement fix"
    template: "bugfix-template"
    
  - name: "Verify fix"
    command: "npm test -- --grep={{test_pattern}}"
```

## Smart Triggers

- On file changes in specific directories
- Scheduled tasks (daily, weekly)
- Git hooks (pre-commit, pre-push)
- Manual activation via commands
MD;
    }

    // Agentic Subagent Content Methods
    private function getAgenticCodebaseAnalyzerContent(): string
    {
        return <<<'MD'
# Codebase Analyzer Subagent

Specialized subagent for analyzing codebase structure and architecture.

## Purpose

Analyze the codebase to understand:
- Project structure and organization
- Key architectural patterns
- Module dependencies
- Code quality indicators

## Analysis Process

1. **Structure Analysis**: Map directory layout and file organization
2. **Pattern Recognition**: Identify design patterns and conventions
3. **Dependency Mapping**: Trace module relationships
4. **Quality Assessment**: Evaluate code health indicators

## Output Format

Provide structured analysis with:
- Architecture overview
- Key components and their roles
- Identified patterns
- Recommendations for improvement
MD;
    }

    private function getAgenticCodebaseLocatorContent(): string
    {
        return <<<'MD'
# Codebase Locator Subagent

Specialized subagent for locating specific code elements.

## Purpose

Find and locate:
- Function and method definitions
- Class declarations
- Variable usages
- Import/export relationships

## Search Strategies

1. **Symbol Search**: Find by name or partial match
2. **Reference Search**: Locate all usages
3. **Definition Jump**: Go to declaration
4. **Pattern Match**: Find by code pattern

## Usage

Provide search query with context about what you're looking for.
Returns file paths, line numbers, and surrounding context.
MD;
    }

    private function getAgenticPatternFinderContent(): string
    {
        return <<<'MD'
# Codebase Pattern Finder Subagent

Identifies recurring patterns and conventions in the codebase.

## Pattern Categories

### Structural Patterns
- Module organization
- File naming conventions
- Directory structure

### Code Patterns
- Error handling approaches
- Data validation patterns
- API design conventions

### Anti-Patterns
- Code duplication
- Inconsistent naming
- Coupling issues

## Analysis Output

Report includes:
- Identified patterns with examples
- Frequency and consistency metrics
- Deviation warnings
- Standardization recommendations
MD;
    }

    private function getAgenticThoughtsAnalyzerContent(): string
    {
        return <<<'MD'
# Thoughts Analyzer Subagent

Analyzes reasoning and decision-making processes.

## Purpose

Review and analyze:
- Problem-solving approaches
- Decision rationale
- Alternative considerations
- Assumption validation

## Analysis Framework

1. **Logic Review**: Verify reasoning chain
2. **Gap Detection**: Identify missing considerations
3. **Bias Check**: Spot cognitive biases
4. **Alternative Generation**: Suggest other approaches

## Output

Structured feedback on thought quality and completeness.
MD;
    }

    private function getAgenticThoughtsLocatorContent(): string
    {
        return <<<'MD'
# Thoughts Locator Subagent

Retrieves relevant thoughts and decisions from context.

## Purpose

Find and surface:
- Previous decisions and rationale
- Related discussions
- Contextual information
- Historical patterns

## Search Capabilities

- Semantic search across conversation history
- Decision tree reconstruction
- Context linking
- Pattern recognition

## Usage

Query with natural language to find relevant prior thoughts.
MD;
    }

    private function getAgenticWebSearchContent(): string
    {
        return <<<'MD'
# Web Search Researcher Subagent

Performs web research for development tasks.

## Research Areas

- Documentation lookup
- Best practices research
- Error resolution
- Library comparisons
- Tutorial discovery

## Search Strategy

1. **Query Formulation**: Craft effective search queries
2. **Source Evaluation**: Prioritize authoritative sources
3. **Information Synthesis**: Combine multiple sources
4. **Relevance Filtering**: Focus on applicable results

## Output Format

- Source citations with URLs
- Key findings summary
- Relevance assessment
- Implementation guidance
MD;
    }

    // TÂCHES Content Methods
    private function getTachesCreateAgentSkillsContent(): string
    {
        return <<<'MD'
# Create Agent Skills

Skill for creating new agent skills with proper structure.

## Skill Structure

```
skills/
  skill-name/
    skill.md          # Main skill definition
    references/       # Supporting documentation
    scripts/          # Automation scripts
    assets/           # Additional resources
```

## Required Elements

1. **Frontmatter**: Metadata and configuration
2. **Description**: Clear purpose statement
3. **Instructions**: Step-by-step guidance
4. **Examples**: Usage demonstrations

## Best Practices

- Single responsibility per skill
- Clear tool allowlist
- Comprehensive documentation
- Test with various scenarios
MD;
    }

    private function getTachesCreateHooksContent(): string
    {
        return <<<'MD'
# Create Hooks

Skill for creating lifecycle hooks.

## Hook Types

- **PreMessage**: Before processing user input
- **PostMessage**: After generating response
- **PreTool**: Before tool execution
- **PostTool**: After tool completion

## Hook Structure

```json
{
  "name": "hook-name",
  "event": "PreMessage",
  "pattern": "regex-pattern",
  "action": "command-or-script"
}
```

## Use Cases

- Automatic formatting
- Validation checks
- Logging and auditing
- Context enrichment
MD;
    }

    private function getTachesCreateMetaPromptsContent(): string
    {
        return <<<'MD'
# Create Meta Prompts

Skill for creating prompts that generate other prompts.

## Meta-Prompt Structure

1. **Template Definition**: Base prompt structure
2. **Variables**: Configurable placeholders
3. **Constraints**: Output requirements
4. **Examples**: Expected outputs

## Template Syntax

```markdown
# {{title}}

Generate a prompt for {{purpose}}.

## Requirements
{{#each requirements}}
- {{this}}
{{/each}}

## Output Format
{{format}}
```

## Best Practices

- Clear variable naming
- Comprehensive examples
- Flexible constraints
- Validation rules
MD;
    }

    private function getTachesCreatePlansContent(): string
    {
        return <<<'MD'
# Create Plans

Skill for creating structured execution plans.

## Plan Structure

```yaml
plan:
  name: "Feature Implementation"
  phases:
    - name: "Research"
      tasks:
        - Analyze requirements
        - Review existing code
    - name: "Implementation"
      tasks:
        - Create components
        - Write tests
    - name: "Review"
      tasks:
        - Code review
        - Documentation
```

## Plan Elements

- **Phases**: Major milestones
- **Tasks**: Actionable items
- **Dependencies**: Task relationships
- **Estimates**: Time/effort predictions

## Best Practices

- Break down into atomic tasks
- Define clear success criteria
- Include validation steps
- Plan for contingencies
MD;
    }

    private function getTachesCreateSlashCommandsContent(): string
    {
        return <<<'MD'
# Create Slash Commands

Skill for creating custom slash commands.

## Command Structure

```markdown
---
description: Brief command description
argument-hint: <required-arg> [optional-arg]
model: claude-sonnet-4-20250514
---

# Command Instructions

Detailed instructions for the command.
```

## Frontmatter Options

- `description`: Short help text
- `argument-hint`: Usage hint
- `model`: Preferred model
- `agent`: Subagent type

## Best Practices

- Clear, concise descriptions
- Helpful argument hints
- Comprehensive instructions
- Error handling guidance
MD;
    }

    private function getTachesCreateSubagentsContent(): string
    {
        return <<<'MD'
# Create Subagents

Skill for creating specialized subagents.

## Subagent Definition

```yaml
name: researcher
description: Research and analysis specialist
capabilities:
  - Web search
  - Document analysis
  - Data synthesis
tools:
  - webfetch
  - read
  - grep
constraints:
  - Read-only operations
  - No code modifications
```

## Design Principles

1. **Single Purpose**: One clear responsibility
2. **Tool Constraints**: Minimal required tools
3. **Clear Boundaries**: Defined scope
4. **Composability**: Work with other agents

## Integration

- Invoke via task delegation
- Pass structured context
- Expect structured output
MD;
    }

    private function getTachesDebugLikeExpertContent(): string
    {
        return <<<'MD'
# Debug Like Expert

Expert debugging skill with systematic approach.

## Debugging Process

### 1. Reproduce
- Confirm the issue exists
- Identify minimal reproduction steps
- Document expected vs actual behavior

### 2. Isolate
- Narrow down the scope
- Identify affected components
- Check recent changes

### 3. Analyze
- Read error messages carefully
- Check logs and stack traces
- Use debugger when needed

### 4. Hypothesize
- Form theories about cause
- Prioritize by likelihood
- Test systematically

### 5. Fix
- Make minimal changes
- Verify the fix
- Prevent regression

## Tools

- `lsp_diagnostics`: Check for errors
- `grep`: Search for patterns
- `bash`: Run tests
- `read`: Examine code

## Anti-Patterns

- Shotgun debugging (random changes)
- Ignoring error messages
- Not testing the fix
- Fixing symptoms, not causes
MD;
    }

    // Claude Codex Settings Content Methods
    private function getCodexPlaywrightTestingContent(): string
    {
        return <<<'MD'
# Playwright Testing Skill

Comprehensive browser automation and E2E testing.

## Setup

```bash
npm init playwright@latest
```

## Test Structure

```typescript
import { test, expect } from '@playwright/test';

test('user can login', async ({ page }) => {
  await page.goto('/login');
  await page.fill('[name="email"]', 'user@example.com');
  await page.fill('[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL('/dashboard');
});
```

## Best Practices

- Use data-testid for selectors
- Wait for network idle
- Use page object model
- Parallel test execution
- Screenshot on failure

## Common Patterns

### Form Testing
```typescript
await page.fill('input[name="email"]', email);
await page.click('button:has-text("Submit")');
```

### Navigation Testing
```typescript
await page.goto('/');
await page.click('a:has-text("About")');
await expect(page).toHaveURL('/about');
```

### API Mocking
```typescript
await page.route('**/api/users', route => {
  route.fulfill({ json: mockUsers });
});
```
MD;
    }

    private function getCodexPluginDevelopmentContent(): string
    {
        return <<<'MD'
# Plugin Development Skill

Creating plugins for Claude Code and Codex.

## Plugin Structure

```
plugin-name/
  package.json
  src/
    index.ts
    commands/
    hooks/
    skills/
  README.md
```

## Package.json

```json
{
  "name": "my-plugin",
  "version": "1.0.0",
  "main": "src/index.ts",
  "claude": {
    "commands": ["src/commands/*.md"],
    "hooks": ["src/hooks/*.json"],
    "skills": ["src/skills/*.md"]
  }
}
```

## Development Workflow

1. Create plugin structure
2. Implement functionality
3. Test locally
4. Document usage
5. Publish/share

## Best Practices

- Follow naming conventions
- Comprehensive documentation
- Error handling
- Version compatibility
- Minimal dependencies
MD;
    }
}
