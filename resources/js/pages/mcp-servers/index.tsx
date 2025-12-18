import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { SeoHead } from '@/components/seo-head';
import type { McpServerIndexPageProps } from '@/types/models';

export default function McpServersIndex({
    mcpServers,
    featuredMcpServers,
}: McpServerIndexPageProps) {
    return (
        <>
            <SeoHead
                title="MCP Servers"
                description="Model Context Protocol server configurations for your AI agents."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                MCP Servers
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Model Context Protocol server configurations for
                                your AI agents
                            </p>
                        </div>
                    </section>

                    {/* Featured Servers */}
                    {featuredMcpServers && featuredMcpServers.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Featured
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {featuredMcpServers.map((server) => (
                                        <McpServerCard
                                            key={server.id}
                                            mcpServer={server}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* All Servers */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                All MCP Servers
                            </h2>
                            {mcpServers.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {mcpServers.map((server) => (
                                        <McpServerCard
                                            key={server.id}
                                            mcpServer={server}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <div className="py-12 text-center text-ds-text-muted">
                                    No MCP servers yet. Be the first to share
                                    one!
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
