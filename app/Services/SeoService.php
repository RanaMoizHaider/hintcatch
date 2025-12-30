<?php

namespace App\Services;

class SeoService
{
    private const SITE_NAME = 'Hint Catch';

    private const TWITTER_SITE = '@hintcatch';

    private const DEFAULT_DESCRIPTION = 'The directory for AI agent configurations. Find and share tools, skills, and MCP server configs for OpenCode, Claude Code, Cursor, and more.';

    public static function getOgImageUrl(): string
    {
        return asset('og-default.png');
    }

    public static function forHome(): array
    {
        return [
            'title' => 'AI Agent Configs',
            'description' => self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => url('/'),
        ];
    }

    public static function forConfig(object $config): array
    {
        return [
            'title' => $config->name,
            'description' => $config->description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('configs.show', $config),
        ];
    }

    public static function forMcpServer(object $mcpServer): array
    {
        return [
            'title' => $mcpServer->name,
            'description' => $mcpServer->description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('mcp-servers.show', $mcpServer),
        ];
    }

    public static function forPrompt(object $prompt): array
    {
        return [
            'title' => $prompt->name,
            'description' => $prompt->description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('prompts.show', $prompt),
        ];
    }

    public static function forSkill(object $skill): array
    {
        return [
            'title' => $skill->name,
            'description' => $skill->description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('skills.show', $skill),
        ];
    }

    public static function forAgent(object $agent): array
    {
        return [
            'title' => $agent->name,
            'description' => $agent->description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('agents.show', $agent),
        ];
    }

    public static function forListing(string $type, string $title, ?string $description = null): array
    {
        return [
            'title' => $title,
            'description' => $description ?? self::DEFAULT_DESCRIPTION,
            'ogImage' => self::getOgImageUrl($title),
            'canonicalUrl' => route("{$type}.index"),
        ];
    }

    public static function forMcpServerIndex(): array
    {
        return self::forListing('mcp-servers', 'MCP Servers', 'Browse MCP servers for AI agents. Find and share Model Context Protocol configurations for Claude, Cursor, OpenCode, and more.');
    }

    public static function forPromptIndex(): array
    {
        return self::forListing('prompts', 'Prompts', 'Browse AI prompts for coding agents. Find and share system prompts for Claude, Cursor, OpenCode, and more.');
    }

    public static function forSkillIndex(): array
    {
        return self::forListing('skills', 'Skills', 'Browse agent skills for AI coding assistants. Find and share reusable skills for OpenCode, Claude Code, and more.');
    }

    public static function forConfigIndex(): array
    {
        return self::forListing('configs', 'Configs', 'Browse AI agent configurations. Find and share commands, rules, hooks, and plugins for coding agents.');
    }

    public static function forConfigTypeIndex(): array
    {
        return [
            'title' => 'Config Types',
            'description' => 'Browse configuration types for AI coding agents. Find commands, rules, hooks, plugins, and more.',
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('config-types.index'),
        ];
    }

    public static function forConfigType(object $configType): array
    {
        return [
            'title' => $configType->name,
            'description' => $configType->description ?? "Browse {$configType->name} configurations for AI coding agents.",
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('config-types.show', $configType),
        ];
    }

    public static function forAgentIndex(): array
    {
        return [
            'title' => 'AI Agents',
            'description' => 'Browse AI coding agents. Find configurations for Claude Code, Cursor, OpenCode, Windsurf, and more.',
            'ogImage' => self::getOgImageUrl(),
            'canonicalUrl' => route('agents.index'),
        ];
    }

    public static function forAgentConfigs(object $agent, object $configType): array
    {
        $title = "{$agent->name} {$configType->name}";

        return [
            'title' => $title,
            'description' => "Browse {$configType->name} configurations for {$agent->name}.",
            'ogImage' => self::getOgImageUrl($title),
            'canonicalUrl' => route('agents.configs', [$agent, $configType]),
        ];
    }

    public static function getDefaults(): array
    {
        return [
            'siteName' => self::SITE_NAME,
            'twitterSite' => self::TWITTER_SITE,
            'defaultImage' => asset('og-default.png'),
            'defaultDescription' => self::DEFAULT_DESCRIPTION,
        ];
    }
}
