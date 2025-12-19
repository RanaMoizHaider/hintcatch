import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import {
    index as configTypesIndex,
    show as showConfigType,
} from '@/actions/App/Http/Controllers/ConfigTypeController';
import { index as mcpServersIndex } from '@/actions/App/Http/Controllers/McpServerController';
import { index as promptsIndex } from '@/actions/App/Http/Controllers/PromptController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import { Icons } from '@/components/ui/icons';
import type { Config, HomePageProps, McpServer, Prompt } from '@/types/models';
import { Link, router } from '@inertiajs/react';
import { ArrowRight, Search } from 'lucide-react';
import { useState } from 'react';

type SortTab = 'recent' | 'top';

interface TabbedSectionProps<T> {
    title: string;
    viewAllHref: string;
    recent: T[];
    top: T[];
    renderItem: (item: T) => React.ReactNode;
    emptyMessage?: string;
}

function TabbedSection<T extends { id: number }>({
    title,
    viewAllHref,
    recent,
    top,
    renderItem,
    emptyMessage = 'No items found',
}: TabbedSectionProps<T>) {
    const [activeTab, setActiveTab] = useState<SortTab>('recent');

    const tabs: { key: SortTab; label: string }[] = [
        { key: 'recent', label: 'Recent' },
        { key: 'top', label: 'Most Liked' },
    ];

    const items = activeTab === 'top' ? top : recent;

    return (
        <section className="border-b-2 border-ds-border">
            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex items-center gap-4">
                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                            {title}
                        </h2>
                        <div className="flex gap-1">
                            {tabs.map((tab) => (
                                <button
                                    key={tab.key}
                                    onClick={() => setActiveTab(tab.key)}
                                    className={`px-3 py-1 text-xs uppercase transition-colors ${
                                        activeTab === tab.key
                                            ? 'border-2 border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                                            : 'border-2 border-transparent text-ds-text-muted hover:text-ds-text-secondary'
                                    }`}
                                >
                                    {tab.label}
                                </button>
                            ))}
                        </div>
                    </div>
                    <Link
                        href={viewAllHref}
                        className="text-xs text-ds-text-muted uppercase transition-colors hover:text-ds-text-primary"
                    >
                        View all
                    </Link>
                </div>
                {items.length > 0 ? (
                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {items.map((item) => (
                            <div key={item.id}>{renderItem(item)}</div>
                        ))}
                    </div>
                ) : (
                    <div className="border-2 border-ds-border bg-ds-bg-card p-8 text-center">
                        <p className="text-ds-text-muted">{emptyMessage}</p>
                    </div>
                )}
            </div>
        </section>
    );
}

export default function Home({
    recentConfigs,
    recentMcpServers,
    recentPrompts,
    topConfigs,
    topMcpServers,
    topPrompts,
    agents,
    configTypes,
    stats,
}: HomePageProps) {
    const [searchQuery, setSearchQuery] = useState('');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        if (searchQuery.trim()) {
            router.get('/search', { q: searchQuery.trim() });
        }
    };

    return (
        <>
            <SeoHead
                title="CLI AI Agent Configs"
                description="The directory for CLI AI agent configurations. Find and share rules, prompts, and MCP server configs for OpenCode, Claude Code, Cursor, and more."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Hero Section with Search */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-12 md:px-6 md:py-20">
                            <div className="mb-6 flex items-center gap-4">
                                <Icons.logo className="h-16 w-16 md:h-20 md:w-20" />
                            </div>
                            <h1 className="text-3xl leading-tight font-normal tracking-tight text-ds-text-primary uppercase md:text-5xl">
                                Catch hints.
                                <br />
                                Ship faster.
                            </h1>
                            <p className="mt-4 max-w-2xl text-lg text-ds-text-secondary">
                                The directory for CLI AI agent configurations.
                                Find and share rules, prompts, and MCP server
                                configs for OpenCode, Claude Code, Cursor, and
                                more.
                            </p>

                            {/* Search Bar */}
                            <form
                                onSubmit={handleSearch}
                                className="mt-8 max-w-xl"
                            >
                                <div className="flex border-2 border-ds-border bg-ds-bg-card focus-within:border-ds-text-muted">
                                    <input
                                        type="text"
                                        value={searchQuery}
                                        onChange={(e) =>
                                            setSearchQuery(e.target.value)
                                        }
                                        placeholder="Search configs, MCP servers, prompts..."
                                        className="flex-1 bg-transparent px-4 py-3 text-ds-text-primary placeholder-ds-text-muted focus:outline-none"
                                    />
                                    <button
                                        type="submit"
                                        className="border-l-2 border-ds-border px-4 text-ds-text-muted transition-colors hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                    >
                                        <Search className="h-5 w-5" />
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    {/* Agents Marquee */}
                    <section className="overflow-hidden border-b-2 border-ds-border">
                        <div className="relative py-4">
                            <div className="animate-marquee flex gap-6 whitespace-nowrap">
                                {/* Duplicate agents for seamless loop */}
                                {[...agents, ...agents].map((agent, index) => (
                                    <Link
                                        key={`${agent.id}-${index}`}
                                        href={showAgent(agent.slug)}
                                        className="group inline-flex items-center gap-3 border-2 border-ds-border bg-ds-bg-card px-4 py-2 transition-colors hover:border-ds-text-muted"
                                    >
                                        <div className="flex h-8 w-8 items-center justify-center text-ds-text-muted">
                                            {agent.logo ? (
                                                <img
                                                    src={agent.logo}
                                                    alt={agent.name}
                                                    className="h-8 w-8"
                                                />
                                            ) : (
                                                <span className="text-sm font-medium">
                                                    {agent.name
                                                        .charAt(0)
                                                        .toUpperCase()}
                                                </span>
                                            )}
                                        </div>
                                        <span className="text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                                            {agent.name}
                                        </span>
                                        <span className="text-xs text-ds-text-muted">
                                            {agent.configs_count ?? 0}
                                        </span>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Configs Section with Tabs */}
                    <TabbedSection<Config>
                        title="Configs"
                        viewAllHref={configTypesIndex.url()}
                        recent={recentConfigs}
                        top={topConfigs}
                        renderItem={(config) => <ConfigCard config={config} />}
                        emptyMessage="No configs found"
                    />

                    {/* MCP Servers Section with Tabs */}
                    <TabbedSection<McpServer>
                        title="MCP Servers"
                        viewAllHref={mcpServersIndex.url()}
                        recent={recentMcpServers}
                        top={topMcpServers}
                        renderItem={(mcpServer) => (
                            <McpServerCard mcpServer={mcpServer} />
                        )}
                        emptyMessage="No MCP servers found"
                    />

                    {/* Prompts Section with Tabs */}
                    <TabbedSection<Prompt>
                        title="Prompts"
                        viewAllHref={promptsIndex.url()}
                        recent={recentPrompts}
                        top={topPrompts}
                        renderItem={(prompt) => <PromptCard prompt={prompt} />}
                        emptyMessage="No prompts found"
                    />

                    {/* Quick Links */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto grid max-w-[1200px] grid-cols-1 md:grid-cols-2">
                            <Link
                                href={mcpServersIndex()}
                                className="group flex items-center justify-between border-b-2 border-ds-border p-6 transition-colors hover:bg-ds-bg-card md:border-r-2 md:border-b-0"
                            >
                                <div>
                                    <h3 className="text-lg font-medium text-ds-text-primary uppercase">
                                        MCP Servers
                                    </h3>
                                    <p className="mt-1 text-sm text-ds-text-muted">
                                        Browse {stats.totalMcpServers} Model
                                        Context Protocol servers
                                    </p>
                                </div>
                                <ArrowRight className="h-5 w-5 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                            </Link>
                            <Link
                                href={promptsIndex()}
                                className="group flex items-center justify-between p-6 transition-colors hover:bg-ds-bg-card"
                            >
                                <div>
                                    <h3 className="text-lg font-medium text-ds-text-primary uppercase">
                                        Prompts
                                    </h3>
                                    <p className="mt-1 text-sm text-ds-text-muted">
                                        Explore {stats.totalPrompts} system
                                        prompts and templates
                                    </p>
                                </div>
                                <ArrowRight className="h-5 w-5 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                            </Link>
                        </div>
                    </section>

                    {/* Stats Bar - Moved to Bottom */}
                    <section className="border-ds-border">
                        <div className="mx-auto grid max-w-[1200px] grid-cols-2 md:grid-cols-4">
                            <div className="border-r-2 border-ds-border px-4 py-4 text-center md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalConfigs}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Configs
                                </div>
                            </div>
                            <div className="border-r-0 border-ds-border px-4 py-4 text-center md:border-r-2 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalMcpServers}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    MCP Servers
                                </div>
                            </div>
                            <div className="border-t-2 border-r-2 border-ds-border px-4 py-4 text-center md:border-t-0 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalPrompts}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Prompts
                                </div>
                            </div>
                            <div className="border-t-2 border-ds-border px-4 py-4 text-center md:border-t-0 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalUsers}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Contributors
                                </div>
                            </div>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
