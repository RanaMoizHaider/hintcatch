import { configs as agentConfigs } from '@/actions/App/Http/Controllers/AgentController';
import { show as showConfigType } from '@/actions/App/Http/Controllers/ConfigTypeController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SearchInput } from '@/components/search-input';
import { SeoHead } from '@/components/seo-head';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { SharedData } from '@/types';
import type { Agent, Config, ConfigType } from '@/types/models';
import { InfiniteScroll, Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface PaginatedConfigs {
    data: Config[];
    next_page_url: string | null;
    prev_page_url: string | null;
}

interface Props {
    configType: ConfigType & { configs_count?: number };
    configs: PaginatedConfigs;
    agents?: (Agent & { configs_count?: number })[];
    filters: {
        search?: string;
    };
}

export default function ConfigTypesShow({
    configType,
    configs,
    agents,
    filters = { search: '' },
}: Props) {
    const { auth } = usePage<SharedData>().props;
    const [search, setSearch] = useState(filters.search || '');
    const submitHref = auth.user ? '/submit' : login();

    const handleSearch = (value: string) => {
        setSearch(value);
        router.get(
            showConfigType.url(configType.slug),
            { search: value || undefined },
            {
                preserveState: true,
                replace: true,
                reset: ['configs'],
            },
        );
    };

    return (
        <>
            <SeoHead
                title={configType.name}
                description={configType.description}
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                        {configType.name}
                                    </h1>
                                    {configType.description && (
                                        <p className="mt-2 text-ds-text-secondary">
                                            {configType.description}
                                        </p>
                                    )}
                                </div>
                                <div className="w-full md:w-64">
                                    <SearchInput
                                        value={search}
                                        onChange={handleSearch}
                                        placeholder="Search configs..."
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {agents && agents.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-6 md:px-6">
                                <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                                    Filter by Agent
                                </h2>
                                <div className="flex flex-wrap gap-2">
                                    {agents.map((agent) => (
                                        <Link
                                            key={agent.id}
                                            href={agentConfigs([
                                                agent.slug,
                                                configType.slug,
                                            ])}
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

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                {search
                                    ? 'Search Results'
                                    : `${configType.configs_count ?? 0} Configs`}
                            </h2>
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
                                            />
                                        ))}
                                    </div>
                                </InfiniteScroll>
                            ) : (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-12 text-center">
                                    <p className="text-ds-text-muted">
                                        {search
                                            ? `No configs found matching "${search}"`
                                            : 'No configs yet. Be the first to share one!'}
                                    </p>
                                    {!search && (
                                        <Button
                                            asChild
                                            className="mt-4 bg-ds-text-primary text-ds-bg-base hover:bg-ds-text-secondary"
                                        >
                                            <Link href={submitHref}>
                                                <Plus className="mr-1 h-4 w-4" />
                                                Submit Now
                                            </Link>
                                        </Button>
                                    )}
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
