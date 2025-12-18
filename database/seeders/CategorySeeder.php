<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ConfigType;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'mcp-servers' => [
                ['name' => 'Database', 'slug' => 'database', 'description' => 'Database connectivity and querying tools'],
                ['name' => 'File System', 'slug' => 'file-system', 'description' => 'Local and remote file system operations'],
                ['name' => 'API & Web', 'slug' => 'api-web', 'description' => 'REST APIs, web scraping, and HTTP tools'],
                ['name' => 'Search & Discovery', 'slug' => 'search-discovery', 'description' => 'Search engines, documentation lookup, and discovery tools'],
                ['name' => 'DevOps & Cloud', 'slug' => 'devops-cloud', 'description' => 'Cloud services, CI/CD, and infrastructure tools'],
                ['name' => 'Version Control', 'slug' => 'version-control', 'description' => 'Git, GitHub, GitLab, and version control operations'],
                ['name' => 'Communication', 'slug' => 'communication', 'description' => 'Slack, Discord, email, and messaging integrations'],
                ['name' => 'AI & LLM', 'slug' => 'ai-llm', 'description' => 'Other AI services and LLM integrations'],
                ['name' => 'Productivity', 'slug' => 'productivity', 'description' => 'Calendar, notes, task management, and productivity tools'],
                ['name' => 'Other', 'slug' => 'other', 'description' => 'Other MCP servers that dont fit other categories'],
            ],
            'rules' => [
                ['name' => 'Framework', 'slug' => 'framework', 'description' => 'Framework-specific rules (Laravel, React, Django, etc.)'],
                ['name' => 'Language', 'slug' => 'language', 'description' => 'Programming language standards and conventions'],
                ['name' => 'Testing', 'slug' => 'testing', 'description' => 'Testing guidelines and quality assurance rules'],
                ['name' => 'Security', 'slug' => 'security', 'description' => 'Security practices and vulnerability prevention'],
                ['name' => 'Documentation', 'slug' => 'documentation', 'description' => 'Documentation standards and commenting guidelines'],
                ['name' => 'Architecture', 'slug' => 'architecture', 'description' => 'Project structure and architectural patterns'],
                ['name' => 'Code Style', 'slug' => 'code-style', 'description' => 'Formatting, naming conventions, and style guides'],
                ['name' => 'Workflow', 'slug' => 'workflow', 'description' => 'Development workflow and process guidelines'],
            ],
            'agents' => [
                ['name' => 'Code Review', 'slug' => 'code-review', 'description' => 'Agents specialized in reviewing and improving code'],
                ['name' => 'Testing', 'slug' => 'testing', 'description' => 'Test generation and quality assurance agents'],
                ['name' => 'Documentation', 'slug' => 'documentation', 'description' => 'Documentation writing and maintenance agents'],
                ['name' => 'Security', 'slug' => 'security', 'description' => 'Security auditing and vulnerability scanning agents'],
                ['name' => 'Refactoring', 'slug' => 'refactoring', 'description' => 'Code refactoring and optimization agents'],
                ['name' => 'Research', 'slug' => 'research', 'description' => 'Research and exploration agents'],
                ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'Deployment, infrastructure, and CI/CD agents'],
            ],
            'plugins' => [
                ['name' => 'Framework', 'slug' => 'framework', 'description' => 'Framework-specific plugin bundles'],
                ['name' => 'Workflow', 'slug' => 'workflow', 'description' => 'Development workflow enhancement plugins'],
                ['name' => 'Tooling', 'slug' => 'tooling', 'description' => 'Developer tooling and utilities'],
            ],
            'custom-tools' => [
                ['name' => 'Database', 'slug' => 'database', 'description' => 'Database interaction tools'],
                ['name' => 'API', 'slug' => 'api', 'description' => 'External API calling tools'],
                ['name' => 'File Operations', 'slug' => 'file-operations', 'description' => 'Advanced file manipulation tools'],
                ['name' => 'Code Analysis', 'slug' => 'code-analysis', 'description' => 'Static analysis and code quality tools'],
                ['name' => 'Utilities', 'slug' => 'utilities', 'description' => 'General purpose utility tools'],
            ],
            'hooks' => [
                ['name' => 'Formatting', 'slug' => 'formatting', 'description' => 'Auto-formatting on file changes'],
                ['name' => 'Validation', 'slug' => 'validation', 'description' => 'Pre-action validation hooks'],
                ['name' => 'Notification', 'slug' => 'notification', 'description' => 'Notification and alerting hooks'],
                ['name' => 'Logging', 'slug' => 'logging', 'description' => 'Action logging and tracking hooks'],
            ],
            'slash-commands' => [
                ['name' => 'Git & Version Control', 'slug' => 'git', 'description' => 'Git operations and version control commands'],
                ['name' => 'Testing', 'slug' => 'testing', 'description' => 'Test running and generation commands'],
                ['name' => 'Deployment', 'slug' => 'deployment', 'description' => 'Deployment and release commands'],
                ['name' => 'Documentation', 'slug' => 'documentation', 'description' => 'Documentation generation commands'],
                ['name' => 'Code Generation', 'slug' => 'code-generation', 'description' => 'Scaffolding and code generation commands'],
                ['name' => 'Utilities', 'slug' => 'utilities', 'description' => 'General utility commands'],
            ],
            'skills' => [
                ['name' => 'File Processing', 'slug' => 'file-processing', 'description' => 'PDF, image, and document processing skills'],
                ['name' => 'Data Analysis', 'slug' => 'data-analysis', 'description' => 'Data parsing and analysis skills'],
                ['name' => 'Integration', 'slug' => 'integration', 'description' => 'Third-party service integration skills'],
                ['name' => 'Automation', 'slug' => 'automation', 'description' => 'Task automation skills'],
            ],
            'prompts' => [
                ['name' => 'System Prompts', 'slug' => 'system', 'description' => 'Initial instructions for agents'],
                ['name' => 'Task Prompts', 'slug' => 'task', 'description' => 'Templates for specific workflows'],
                ['name' => 'Code Review', 'slug' => 'code-review', 'description' => 'Structured code review templates'],
                ['name' => 'Documentation', 'slug' => 'documentation', 'description' => 'Documentation generation templates'],
                ['name' => 'Debugging', 'slug' => 'debugging', 'description' => 'Debugging and troubleshooting prompts'],
                ['name' => 'Refactoring', 'slug' => 'refactoring', 'description' => 'Code refactoring prompt templates'],
            ],
        ];

        foreach ($categories as $configTypeSlug => $categoryList) {
            $configType = ConfigType::where('slug', $configTypeSlug)->first();

            if (! $configType) {
                continue;
            }

            foreach ($categoryList as $categoryData) {
                Category::updateOrCreate(
                    [
                        'slug' => $categoryData['slug'],
                        'config_type_id' => $configType->id,
                    ],
                    [
                        'name' => $categoryData['name'],
                        'description' => $categoryData['description'],
                    ]
                );
            }
        }
    }
}
