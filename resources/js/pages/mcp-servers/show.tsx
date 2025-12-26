import { CommentSection } from '@/components/comment-section';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { SeoHead } from '@/components/seo-head';
import { ShowPageHeader } from '@/components/show-page-header';
import type { McpServerShowPageProps } from '@/types/models';
import {
    Check,
    Copy,
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
                    <ShowPageHeader
                        type="mcp-server"
                        name={mcpServer.name}
                        description={mcpServer.description}
                        voteScore={mcpServer.vote_score}
                        userVote={interaction.user_vote}
                        votableId={mcpServer.id}
                        isFavorited={interaction.is_favorited}
                        favoritesCount={interaction.favorites_count}
                        submitterUser={mcpServer.submitter}
                        sourceAuthor={mcpServer.source_author}
                        githubUrl={mcpServer.github_url}
                        sourceUrl={mcpServer.source_url}
                        mcpServerType={mcpServer.type}
                        icon={
                            mcpServer.type === 'remote' ? (
                                <Globe className="h-6 w-6 text-ds-text-muted" />
                            ) : (
                                <Terminal className="h-6 w-6 text-ds-text-muted" />
                            )
                        }
                    />

                    {agentSlugs.length > 0 && activeIntegration && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Integration by Agent
                                </h2>

                                <div className="border-2 border-ds-border bg-ds-bg-card">
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

                    <CommentSection
                        commentableType="mcp-server"
                        commentableId={mcpServer.id}
                        comments={comments}
                    />

                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {mcpServer.submitter?.name}
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
