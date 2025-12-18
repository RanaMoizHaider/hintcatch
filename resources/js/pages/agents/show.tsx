import { configs as agentConfigs } from '@/actions/App/Http/Controllers/AgentController';
import { index as mcpServersIndex } from '@/actions/App/Http/Controllers/McpServerController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import type { AgentShowPageProps, Config } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowRight, ExternalLink, Server } from 'lucide-react';
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

export default function AgentsShow({
    agent,
    configTypes,
    configsByType,
    mcpServerCount,
}: AgentShowPageProps) {
    // SEO-friendly title and description
    const totalConfigs = configTypes.reduce(
        (sum, ct) => sum + (ct.configs_count ?? 0),
        0,
    );
    const pageTitle = `${agent.name} Plugins & Extensions`;
    const pageDescription = `Discover ${totalConfigs} configs${agent.supports_mcp ? ` and ${mcpServerCount} MCP servers` : ''} for ${agent.name}. Enhance your AI coding workflow with community-curated extensions.`;

    return (
        <>
            <SeoHead
                title={pageTitle}
                description={pageDescription}
                ogType="website"
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Agent Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex items-start gap-6">
                                <div className="flex h-20 w-20 shrink-0 items-center justify-center bg-ds-bg-secondary text-ds-text-muted">
                                    {agent.logo ? (
                                        <img
                                            src={agent.logo}
                                            alt={agent.name}
                                            className="h-12 w-12"
                                        />
                                    ) : (
                                        <span className="text-3xl font-medium">
                                            {agent.name.charAt(0).toUpperCase()}
                                        </span>
                                    )}
                                </div>
                                <div className="flex-1">
                                    <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                        {agent.name}
                                    </h1>
                                    {agent.description && (
                                        <p className="mt-2 text-ds-text-secondary">
                                            {agent.description}
                                        </p>
                                    )}
                                    <div className="mt-4 flex flex-wrap gap-3">
                                        {agent.website && (
                                            <a
                                                href={agent.website}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                            >
                                                <ExternalLink className="h-3 w-3" />
                                                Website
                                            </a>
                                        )}
                                        {agent.docs_url && (
                                            <a
                                                href={agent.docs_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                            >
                                                <ExternalLink className="h-3 w-3" />
                                                Docs
                                            </a>
                                        )}
                                        {agent.github_url && (
                                            <a
                                                href={agent.github_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                            >
                                                <ExternalLink className="h-3 w-3" />
                                                GitHub
                                            </a>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {Object.entries(configsByType).map(
                        ([slug, { configType, recent, top }]) => (
                            <TabbedSection<Config>
                                key={slug}
                                title={configType.name}
                                viewAllHref={agentConfigs.url([
                                    agent.slug,
                                    slug,
                                ])}
                                recent={recent}
                                top={top}
                                renderItem={(config) => (
                                    <ConfigCard config={config} />
                                )}
                                emptyMessage={`No ${configType.name.toLowerCase()} found`}
                            />
                        ),
                    )}

                    {agent.supports_mcp && mcpServerCount !== undefined && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px]">
                                <Link
                                    href={mcpServersIndex()}
                                    className="group flex items-center justify-between p-6 transition-colors hover:bg-ds-bg-card"
                                >
                                    <div className="flex items-center gap-4">
                                        <Server className="h-6 w-6 text-ds-text-muted" />
                                        <div>
                                            <h3 className="font-medium text-ds-text-primary uppercase">
                                                MCP Servers
                                            </h3>
                                            <p className="text-sm text-ds-text-muted">
                                                {mcpServerCount} available
                                                servers
                                            </p>
                                        </div>
                                    </div>
                                    <ArrowRight className="h-5 w-5 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                                </Link>
                            </div>
                        </section>
                    )}

                    <section className="border-ds-border">
                        <div
                            className={`mx-auto grid max-w-[1200px] ${
                                agent.supports_mcp &&
                                mcpServerCount !== undefined
                                    ? 'grid-cols-2'
                                    : 'grid-cols-1'
                            }`}
                        >
                            <div
                                className={`${
                                    agent.supports_mcp &&
                                    mcpServerCount !== undefined
                                        ? 'border-r-2 border-ds-border'
                                        : ''
                                } px-4 py-4 text-center md:px-6`}
                            >
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {totalConfigs}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Configs
                                </div>
                            </div>
                            {agent.supports_mcp &&
                                mcpServerCount !== undefined && (
                                    <div className="px-4 py-4 text-center md:px-6">
                                        <div className="text-2xl font-medium text-ds-text-primary">
                                            {mcpServerCount}
                                        </div>
                                        <div className="text-xs text-ds-text-muted uppercase">
                                            MCP Servers
                                        </div>
                                    </div>
                                )}
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
