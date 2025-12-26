<?php

namespace Database\Seeders;

use App\Models\McpServer;
use App\Models\User;
use Illuminate\Database\Seeder;

class McpServerSeeder extends Seeder
{
    public function run(): void
    {
        $systemUser = User::where('username', 'ranamoizhaider')->first();

        $mcpServers = [
            [
                'name' => 'GitHub MCP Server',
                'slug' => 'github-mcp-server',
                'description' => 'GitHub\'s official MCP Server for repository management, issue tracking, and workflow automation.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['@github/mcp-server'],
                'env' => ['GITHUB_PERSONAL_ACCESS_TOKEN' => ''],
                'source_url' => 'https://github.com/github/github-mcp-server',
                'source_author' => 'GitHub',
                'is_featured' => false,
            ],
            [
                'name' => 'Grep MCP Server',
                'slug' => 'grep-mcp-server',
                'description' => 'Search across a million GitHub repositories through a standard MCP interface.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['@vercel/grep-mcp-server'],
                'source_url' => 'https://github.com/vercel/grep-mcp-server',
                'source_author' => 'Vercel',
                'is_featured' => false,
            ],
            [
                'name' => 'Filesystem MCP Server',
                'slug' => 'filesystem-mcp-server',
                'description' => 'Secure file operations with configurable access controls.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-filesystem', '/path/to/allowed/dir'],
                'source_url' => 'https://github.com/modelcontextprotocol/servers',
                'source_author' => 'Anthropic',
                'is_featured' => false,
            ],
            [
                'name' => 'Brave Search MCP Server',
                'slug' => 'brave-search-mcp-server',
                'description' => 'Web and local search using Brave\'s Search API.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-brave-search'],
                'env' => ['BRAVE_API_KEY' => ''],
                'source_url' => 'https://github.com/modelcontextprotocol/servers',
                'source_author' => 'Anthropic',
                'is_featured' => false,
            ],
            [
                'name' => 'Puppeteer MCP Server',
                'slug' => 'puppeteer-mcp-server',
                'description' => 'Browser automation and web scraping capabilities.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@modelcontextprotocol/server-puppeteer'],
                'source_url' => 'https://github.com/modelcontextprotocol/servers',
                'source_author' => 'Anthropic',
                'is_featured' => false,
            ],
            // Claude Codex Settings MCP Servers - https://github.com/fcakyon/claude-codex-settings
            [
                'name' => 'Azure MCP Server',
                'slug' => 'azure-mcp-server',
                'description' => 'Azure cloud services integration for storage, compute, and AI services.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@azure/mcp-server'],
                'env' => ['AZURE_SUBSCRIPTION_ID' => '', 'AZURE_TENANT_ID' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'MongoDB MCP Server',
                'slug' => 'mongodb-mcp-server',
                'description' => 'MongoDB database operations including CRUD, aggregation, and schema management.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@mongodb/mcp-server'],
                'env' => ['MONGODB_URI' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Supabase MCP Server',
                'slug' => 'supabase-mcp-server',
                'description' => 'Supabase integration for database, auth, storage, and edge functions.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@supabase/mcp-server'],
                'env' => ['SUPABASE_URL' => '', 'SUPABASE_KEY' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Tavily MCP Server',
                'slug' => 'tavily-mcp-server',
                'description' => 'AI-powered web search optimized for LLM applications.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@tavily/mcp-server'],
                'env' => ['TAVILY_API_KEY' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Playwright MCP Server',
                'slug' => 'playwright-mcp-server',
                'description' => 'Browser automation with Playwright for testing and web scraping.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@playwright/mcp-server'],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Slack MCP Server',
                'slug' => 'slack-mcp-server',
                'description' => 'Slack integration for messaging, channels, and workflow automation.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@slack/mcp-server'],
                'env' => ['SLACK_BOT_TOKEN' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Linear MCP Server',
                'slug' => 'linear-mcp-server',
                'description' => 'Linear issue tracking integration for project management.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@linear/mcp-server'],
                'env' => ['LINEAR_API_KEY' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Google Cloud MCP Server',
                'slug' => 'google-cloud-mcp-server',
                'description' => 'Google Cloud Platform services including GCS, BigQuery, and Vertex AI.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@google-cloud/mcp-server'],
                'env' => ['GOOGLE_APPLICATION_CREDENTIALS' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
            [
                'name' => 'Paper Search MCP Server',
                'slug' => 'paper-search-mcp-server',
                'description' => 'Academic paper search across arXiv, Semantic Scholar, and other sources.',
                'type' => 'local',
                'command' => 'npx',
                'args' => ['-y', '@semantic-scholar/mcp-server'],
                'env' => ['SEMANTIC_SCHOLAR_API_KEY' => ''],
                'source_url' => 'https://github.com/fcakyon/claude-codex-settings',
                'source_author' => 'Fatih Akyon',
                'is_featured' => false,
            ],
        ];

        foreach ($mcpServers as $data) {
            McpServer::updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'submitted_by' => $systemUser->id]
            );
        }
    }
}
