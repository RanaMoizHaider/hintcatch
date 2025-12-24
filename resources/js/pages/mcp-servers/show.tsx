import { show as showUser } from '@/actions/App/Http/Controllers/UserProfileController';
import { CommentSection } from '@/components/comment-section';
import { FavoriteButton } from '@/components/favorite-button';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { SeoHead } from '@/components/seo-head';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useInitials } from '@/hooks/use-initials';
import type { McpServerShowPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';
import {
    ArrowUp,
    Check,
    Copy,
    Download,
    ExternalLink,
    FileCode,
    FolderOpen,
    Globe,
    Terminal,
} from 'lucide-react';
import { useState } from 'react';

export default function McpServersShow({
    mcpServer,
    agentIntegrations,
    moreFromUser,
    comments,
    interaction,
}: McpServerShowPageProps) {
    const getInitials = useInitials();
    const agentSlugs = Object.keys(agentIntegrations);
    const [activeAgent, setActiveAgent] = useState<string>(agentSlugs[0] ?? '');
    const [copiedJson, setCopiedJson] = useState(false);
    const [copiedCli, setCopiedCli] = useState(false);

    const activeIntegration = agentIntegrations[activeAgent];

    const handleCopyJson = async () => {
        const config = activeIntegration?.integration.json_config;
        if (!config) return;
        await navigator.clipboard.writeText(JSON.stringify(config, null, 2));
        setCopiedJson(true);
        setTimeout(() => setCopiedJson(false), 2000);
    };

    const handleCopyCli = async () => {
        const cliCommand = activeIntegration?.integration.cli_command;
        if (!cliCommand) return;
        await navigator.clipboard.writeText(cliCommand);
        setCopiedCli(true);
        setTimeout(() => setCopiedCli(false), 2000);
    };

    return (
        <>
            <SeoHead
                title={mcpServer.name}
                description={mcpServer.description}
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div className="flex-1">
                                    <div className="flex items-center gap-3">
                                        {mcpServer.type === 'remote' ? (
                                            <Globe className="h-6 w-6 text-ds-text-muted" />
                                        ) : (
                                            <Terminal className="h-6 w-6 text-ds-text-muted" />
                                        )}
                                        <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                            {mcpServer.name}
                                        </h1>
                                    </div>
                                    {mcpServer.description && (
                                        <p className="mt-2 text-ds-text-secondary">
                                            {mcpServer.description}
                                        </p>
                                    )}
                                    <div className="mt-4">
                                        <Badge
                                            variant="outline"
                                            className="border-ds-border text-ds-text-secondary"
                                        >
                                            {mcpServer.type}
                                        </Badge>
                                    </div>
                                </div>
                                <div className="flex items-center gap-6 text-sm text-ds-text-muted">
                                    <div className="flex items-center gap-1">
                                        <ArrowUp className="h-4 w-4" />
                                        <span>{mcpServer.vote_score}</span>
                                    </div>
                                    <FavoriteButton
                                        favorableType="mcp-server"
                                        favorableId={mcpServer.id}
                                        isFavorited={interaction.is_favorited}
                                        favoritesCount={
                                            interaction.favorites_count
                                        }
                                    />
                                    <div className="flex items-center gap-1">
                                        <Download className="h-4 w-4" />
                                        <span>{mcpServer.downloads}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Author */}
                            {mcpServer.user && (
                                <div className="mt-6 flex items-center gap-3">
                                    <Link
                                        href={showUser(mcpServer.user.username)}
                                    >
                                        <Avatar className="h-8 w-8">
                                            <AvatarImage
                                                src={
                                                    mcpServer.user.avatar ??
                                                    undefined
                                                }
                                                alt={mcpServer.user.name}
                                            />
                                            <AvatarFallback className="bg-ds-bg-secondary text-xs text-ds-text-muted">
                                                {getInitials(
                                                    mcpServer.user.name,
                                                )}
                                            </AvatarFallback>
                                        </Avatar>
                                    </Link>
                                    <div>
                                        <Link
                                            href={showUser(
                                                mcpServer.user.username,
                                            )}
                                            className="text-sm text-ds-text-primary hover:text-ds-text-secondary"
                                        >
                                            {mcpServer.user.name}
                                        </Link>
                                        <div className="text-xs text-ds-text-muted">
                                            @{mcpServer.user.username}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Source */}
                            {mcpServer.source_url && (
                                <div className="mt-4">
                                    <a
                                        href={mcpServer.source_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        <ExternalLink className="h-3 w-3" />
                                        Source
                                        {mcpServer.source_author &&
                                            ` by ${mcpServer.source_author}`}
                                    </a>
                                </div>
                            )}
                        </div>
                    </section>

                    {/* Agent-specific integrations */}
                    {agentSlugs.length > 0 && activeIntegration && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Integration by Agent
                                </h2>

                                <div className="border-2 border-ds-border bg-ds-bg-card">
                                    {/* Agent Tabs */}
                                    <div className="flex flex-wrap border-b-2 border-ds-border">
                                        {agentSlugs.map((slug) => {
                                            const { agent } =
                                                agentIntegrations[slug];
                                            return (
                                                <button
                                                    key={slug}
                                                    type="button"
                                                    onClick={() =>
                                                        setActiveAgent(slug)
                                                    }
                                                    className={`border-r-2 border-ds-border px-4 py-2 text-xs transition-colors ${
                                                        activeAgent === slug
                                                            ? 'bg-ds-bg-secondary text-ds-text-primary'
                                                            : 'text-ds-text-muted hover:bg-ds-bg-secondary hover:text-ds-text-primary'
                                                    }`}
                                                >
                                                    {agent.name}
                                                </button>
                                            );
                                        })}
                                    </div>

                                    {/* Config File Paths */}
                                    <div className="border-b-2 border-ds-border p-4">
                                        <div className="mb-3 flex items-center gap-2 text-xs font-medium text-ds-text-muted">
                                            <FolderOpen className="h-3.5 w-3.5" />
                                            <span>Config File Locations</span>
                                        </div>
                                        <div className="space-y-2">
                                            {activeIntegration.integration
                                                .config_paths.project && (
                                                <div className="flex items-start gap-2">
                                                    <span className="shrink-0 rounded bg-ds-bg-secondary px-1.5 py-0.5 text-xs text-ds-text-muted">
                                                        Project
                                                    </span>
                                                    <code className="text-sm text-ds-text-primary">
                                                        {
                                                            activeIntegration
                                                                .integration
                                                                .config_paths
                                                                .project
                                                        }
                                                    </code>
                                                </div>
                                            )}
                                            {activeIntegration.integration
                                                .config_paths.global && (
                                                <div className="flex items-start gap-2">
                                                    <span className="shrink-0 rounded bg-ds-bg-secondary px-1.5 py-0.5 text-xs text-ds-text-muted">
                                                        Global
                                                    </span>
                                                    <code className="text-sm text-ds-text-primary">
                                                        {
                                                            activeIntegration
                                                                .integration
                                                                .config_paths
                                                                .global
                                                        }
                                                    </code>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* CLI Command (if available) */}
                                    {activeIntegration.integration
                                        .cli_command && (
                                        <div className="border-b-2 border-ds-border">
                                            <div className="flex items-center justify-between border-b border-ds-border/50 px-4 py-2">
                                                <div className="flex items-center gap-2 text-xs text-ds-text-muted">
                                                    <Terminal className="h-3.5 w-3.5" />
                                                    <span>CLI Command</span>
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={handleCopyCli}
                                                    className="flex items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                                >
                                                    {copiedCli ? (
                                                        <>
                                                            <Check className="h-3 w-3" />
                                                            Copied
                                                        </>
                                                    ) : (
                                                        <>
                                                            <Copy className="h-3 w-3" />
                                                            Copy
                                                        </>
                                                    )}
                                                </button>
                                            </div>
                                            <div className="overflow-x-auto bg-ds-bg-secondary/50 p-4">
                                                <pre className="text-sm leading-relaxed text-ds-text-primary">
                                                    <code>
                                                        {
                                                            activeIntegration
                                                                .integration
                                                                .cli_command
                                                        }
                                                    </code>
                                                </pre>
                                            </div>
                                        </div>
                                    )}

                                    {/* JSON Config Header */}
                                    <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                                        <div className="flex items-center gap-2 text-xs text-ds-text-muted">
                                            <FileCode className="h-3.5 w-3.5" />
                                            <span>JSON Configuration</span>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={handleCopyJson}
                                            className="flex items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                        >
                                            {copiedJson ? (
                                                <>
                                                    <Check className="h-3 w-3" />
                                                    Copied
                                                </>
                                            ) : (
                                                <>
                                                    <Copy className="h-3 w-3" />
                                                    Copy
                                                </>
                                            )}
                                        </button>
                                    </div>

                                    {/* Config JSON */}
                                    <div className="overflow-x-auto">
                                        <pre className="p-4 text-sm leading-relaxed text-ds-text-primary">
                                            <code>
                                                {JSON.stringify(
                                                    activeIntegration
                                                        .integration
                                                        .json_config,
                                                    null,
                                                    2,
                                                )}
                                            </code>
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Comments */}
                    <CommentSection
                        commentableType="mcp-server"
                        commentableId={mcpServer.id}
                        comments={comments}
                    />

                    {/* More from User */}
                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {mcpServer.user?.name}
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    {moreFromUser.map((server) => (
                                        <McpServerCard
                                            key={server.id}
                                            mcpServer={server}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
