import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import type { AgentIndexPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';

export default function AgentsIndex({ agents }: AgentIndexPageProps) {
    return (
        <>
            <SeoHead
                title="AI Agents"
                description="Browse configurations for your favorite AI agents."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                AI Agents
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Browse configurations for your favorite AI
                                agents
                            </p>
                        </div>
                    </section>

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                                {agents.map((agent) => (
                                    <Link
                                        key={agent.id}
                                        href={showAgent(agent.slug)}
                                        className="group flex flex-col items-center gap-3 border-2 border-ds-border bg-ds-bg-card p-6 transition-colors hover:border-ds-text-muted"
                                    >
                                        <div className="flex h-16 w-16 items-center justify-center rounded text-ds-text-muted">
                                            {agent.logo ? (
                                                <img
                                                    src={agent.logo}
                                                    alt={agent.name}
                                                    className="h-16 w-16"
                                                />
                                            ) : (
                                                <span className="text-2xl font-medium">
                                                    {agent.name
                                                        .charAt(0)
                                                        .toUpperCase()}
                                                </span>
                                            )}
                                        </div>
                                        <div className="text-center">
                                            <div className="text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                                                {agent.name}
                                            </div>
                                            <div className="mt-1 text-xs text-ds-text-muted">
                                                {agent.configs_count ?? 0}{' '}
                                                configs
                                            </div>
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
