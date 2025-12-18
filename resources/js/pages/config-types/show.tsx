import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import { Badge } from '@/components/ui/badge';
import type { ConfigTypeShowPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';

export default function ConfigTypesShow({
    configType,
    configs,
    categories,
    agents,
}: ConfigTypeShowPageProps) {
    return (
        <>
            <SeoHead
                title={configType.name}
                description={configType.description}
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                {configType.name}
                            </h1>
                            {configType.description && (
                                <p className="mt-2 text-ds-text-secondary">
                                    {configType.description}
                                </p>
                            )}
                        </div>
                    </section>

                    {/* Agents that support this type */}
                    {agents.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-6 md:px-6">
                                <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                                    Supported Agents
                                </h2>
                                <div className="flex flex-wrap gap-2">
                                    {agents.map((agent) => (
                                        <Link
                                            key={agent.id}
                                            href={showAgent(agent.slug)}
                                        >
                                            <Badge
                                                variant="outline"
                                                className="border-ds-border text-ds-text-secondary hover:border-ds-text-muted hover:text-ds-text-primary"
                                            >
                                                {agent.name} (
                                                {agent.configs_count ?? 0})
                                            </Badge>
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Categories */}
                    {categories.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-6 md:px-6">
                                <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                                    Categories
                                </h2>
                                <div className="flex flex-wrap gap-2">
                                    {categories.map((category) => (
                                        <Badge
                                            key={category.id}
                                            variant="outline"
                                            className="border-ds-border text-ds-text-secondary"
                                        >
                                            {category.name} (
                                            {category.configs_count ?? 0})
                                        </Badge>
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Configs */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                {configType.configs_count ?? 0} Configs
                            </h2>
                            {configs.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {configs.map((config) => (
                                        <ConfigCard
                                            key={config.id}
                                            config={config}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <div className="py-12 text-center text-ds-text-muted">
                                    No configs yet. Be the first to share one!
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
