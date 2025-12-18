import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import type { AgentConfigsPageProps } from '@/types/models';
import { Link, router } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export default function AgentConfigs({
    agent,
    configType,
    configs,
    categories,
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

    const handleCategoryChange = (category: string | null) => {
        router.get(
            window.location.pathname,
            { ...filters, category },
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

                                {/* Category Filter */}
                                {categories.length > 0 && (
                                    <div className="flex flex-wrap items-center gap-2">
                                        <span className="text-xs text-ds-text-muted uppercase">
                                            Category:
                                        </span>
                                        <button
                                            onClick={() =>
                                                handleCategoryChange(null)
                                            }
                                            className={`text-xs transition-colors ${
                                                !filters.category
                                                    ? 'text-ds-text-primary underline'
                                                    : 'text-ds-text-muted hover:text-ds-text-secondary'
                                            }`}
                                        >
                                            All
                                        </button>
                                        {categories.map((cat) => (
                                            <button
                                                key={cat.id}
                                                onClick={() =>
                                                    handleCategoryChange(
                                                        cat.slug,
                                                    )
                                                }
                                                className={`text-xs transition-colors ${
                                                    filters.category ===
                                                    cat.slug
                                                        ? 'text-ds-text-primary underline'
                                                        : 'text-ds-text-muted hover:text-ds-text-secondary'
                                                }`}
                                            >
                                                {cat.name} (
                                                {cat.configs_count ?? 0})
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>

                    {/* Configs Grid */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            {configs.data.length > 0 ? (
                                <>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {configs.data.map((config) => (
                                            <ConfigCard
                                                key={config.id}
                                                config={config}
                                                showAgent={false}
                                            />
                                        ))}
                                    </div>

                                    {/* Pagination */}
                                    {configs.meta.last_page > 1 && (
                                        <div className="mt-8 flex items-center justify-center gap-2">
                                            {configs.links.prev && (
                                                <Link
                                                    href={configs.links.prev}
                                                    className="flex items-center gap-1 border-2 border-ds-border px-3 py-1 text-sm text-ds-text-muted transition-colors hover:border-ds-text-muted hover:text-ds-text-primary"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    Previous
                                                </Link>
                                            )}
                                            <span className="px-3 py-1 text-sm text-ds-text-muted">
                                                Page {configs.meta.current_page}{' '}
                                                of {configs.meta.last_page}
                                            </span>
                                            {configs.links.next && (
                                                <Link
                                                    href={configs.links.next}
                                                    className="flex items-center gap-1 border-2 border-ds-border px-3 py-1 text-sm text-ds-text-muted transition-colors hover:border-ds-text-muted hover:text-ds-text-primary"
                                                >
                                                    Next
                                                    <ChevronRight className="h-4 w-4" />
                                                </Link>
                                            )}
                                        </div>
                                    )}
                                </>
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
