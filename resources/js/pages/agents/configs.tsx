import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import type { AgentConfigsPageProps } from '@/types/models';
import { InfiniteScroll, Link, router } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

export default function AgentConfigs({
    agent,
    configType,
    configs,
    filters,
    totalCount,
}: AgentConfigsPageProps) {
    // SEO-friendly title and description
    const pageTitle = `${configType.name} for ${agent.name} | ${totalCount} Configs`;
    const pageDescription = `Browse ${totalCount} ${configType.name} for ${agent.name}. ${configType.description}`;

    const handleSortChange = (sort: 'recent' | 'top') => {
        router.get(
            window.location.pathname,
            { ...filters, sort },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <>
            <SeoHead title={pageTitle} description={pageDescription} />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Breadcrumb & Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            {/* Breadcrumb */}
                            <div className="mb-4 flex items-center gap-2 text-sm text-ds-text-muted">
                                <Link
                                    href={showAgent(agent.slug)}
                                    className="transition-colors hover:text-ds-text-primary"
                                >
                                    {agent.name}
                                </Link>
                                <ChevronRight className="h-3 w-3" />
                                <span className="text-ds-text-secondary">
                                    {configType.name}
                                </span>
                            </div>

                            <div className="flex items-start gap-4">
                                <div className="flex h-16 w-16 shrink-0 items-center justify-center bg-ds-bg-secondary text-ds-text-muted">
                                    {agent.logo ? (
                                        <img
                                            src={agent.logo}
                                            alt={agent.name}
                                            className="h-10 w-10"
                                        />
                                    ) : (
                                        <span className="text-2xl font-medium">
                                            {agent.name.charAt(0).toUpperCase()}
                                        </span>
                                    )}
                                </div>
                                <div className="flex-1">
                                    <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                        {configType.name} for {agent.name}
                                    </h1>
                                    <p className="mt-2 text-ds-text-secondary">
                                        {configType.description}
                                    </p>
                                    <p className="mt-1 text-sm text-ds-text-muted">
                                        {totalCount}{' '}
                                        {totalCount === 1
                                            ? 'config'
                                            : 'configs'}{' '}
                                        available
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Filters */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-4 md:px-6">
                            <div className="flex flex-wrap items-center justify-between gap-4">
                                {/* Sort Tabs */}
                                <div className="flex gap-1">
                                    <button
                                        onClick={() =>
                                            handleSortChange('recent')
                                        }
                                        className={`px-3 py-1 text-xs uppercase transition-colors ${
                                            filters.sort === 'recent'
                                                ? 'bg-ds-bg-secondary text-ds-text-primary'
                                                : 'text-ds-text-muted hover:text-ds-text-secondary'
                                        }`}
                                    >
                                        Recent
                                    </button>
                                    <button
                                        onClick={() => handleSortChange('top')}
                                        className={`px-3 py-1 text-xs uppercase transition-colors ${
                                            filters.sort === 'top'
                                                ? 'bg-ds-bg-secondary text-ds-text-primary'
                                                : 'text-ds-text-muted hover:text-ds-text-secondary'
                                        }`}
                                    >
                                        Most Liked
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Configs Grid */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            {configs.data.length > 0 ? (
                                <InfiniteScroll
                                    data="configs"
                                    buffer={500}
                                    loading={
                                        <div className="mt-8 flex justify-center">
                                            <div className="h-6 w-6 animate-spin rounded-full border-2 border-ds-border border-t-ds-text-primary" />
                                        </div>
                                    }
                                >
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {configs.data.map((config) => (
                                            <ConfigCard
                                                key={config.id}
                                                config={config}
                                                showAgent={false}
                                            />
                                        ))}
                                    </div>
                                </InfiniteScroll>
                            ) : (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-12 text-center">
                                    <p className="text-ds-text-muted">
                                        No {configType.name.toLowerCase()} found
                                        for {agent.name} yet.
                                    </p>
                                    <p className="mt-2 text-sm text-ds-text-muted">
                                        Be the first to share one!
                                    </p>
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
