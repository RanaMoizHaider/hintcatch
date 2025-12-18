import search from '@/actions/App/Http/Controllers/SearchController';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import type { SearchPageProps } from '@/types/models';
import { router } from '@inertiajs/react';
import { Filter, Search, X } from 'lucide-react';
import { useState } from 'react';

type ContentType = 'all' | 'configs' | 'mcp-servers' | 'prompts';
type SortOption = 'recent' | 'top';

interface FilterModalProps {
    isOpen: boolean;
    onClose: () => void;
    filters: SearchPageProps['filters'];
    agents: SearchPageProps['agents'];
    configTypes: SearchPageProps['configTypes'];
    categories: SearchPageProps['categories'];
    promptCategories: SearchPageProps['promptCategories'];
    onApply: (filters: Partial<SearchPageProps['filters']>) => void;
}

function FilterModal({
    isOpen,
    onClose,
    filters,
    agents,
    configTypes,
    categories,
    promptCategories,
    onApply,
}: FilterModalProps) {
    const [localFilters, setLocalFilters] = useState(filters);

    if (!isOpen) return null;

    const filteredCategories = localFilters.config_type
        ? categories.filter(
              (c) => c.config_type_id === Number(localFilters.config_type),
          )
        : categories;

    const handleApply = () => {
        onApply(localFilters);
        onClose();
    };

    const handleReset = () => {
        const resetFilters = {
            type: 'all' as const,
            agent: null,
            config_type: null,
            category: null,
            prompt_category: null,
            sort: 'recent' as const,
        };
        setLocalFilters(resetFilters);
        onApply(resetFilters);
        onClose();
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
            {/* Backdrop */}
            <div
                className="absolute inset-0 bg-black/60"
                onClick={onClose}
            ></div>

            {/* Modal */}
            <div className="relative z-10 w-full max-w-lg border-2 border-ds-border bg-ds-bg-card p-6">
                <div className="mb-6 flex items-center justify-between">
                    <h2 className="text-lg font-medium text-ds-text-primary uppercase">
                        Filters
                    </h2>
                    <button
                        onClick={onClose}
                        className="text-ds-text-muted hover:text-ds-text-primary"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                <div className="space-y-6">
                    {/* Content Type */}
                    <div>
                        <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                            Content Type
                        </label>
                        <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            {(
                                [
                                    'all',
                                    'configs',
                                    'mcp-servers',
                                    'prompts',
                                ] as ContentType[]
                            ).map((type) => (
                                <button
                                    key={type}
                                    onClick={() =>
                                        setLocalFilters({
                                            ...localFilters,
                                            type,
                                        })
                                    }
                                    className={`border-2 px-3 py-2 text-xs uppercase transition-colors ${
                                        localFilters.type === type
                                            ? 'border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                                            : 'border-ds-border text-ds-text-muted hover:border-ds-text-muted'
                                    }`}
                                >
                                    {type === 'mcp-servers' ? 'MCP' : type}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Sort */}
                    <div>
                        <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                            Sort By
                        </label>
                        <div className="grid grid-cols-2 gap-2">
                            {(['recent', 'top'] as SortOption[]).map((sort) => (
                                <button
                                    key={sort}
                                    onClick={() =>
                                        setLocalFilters({
                                            ...localFilters,
                                            sort,
                                        })
                                    }
                                    className={`border-2 px-3 py-2 text-xs uppercase transition-colors ${
                                        localFilters.sort === sort
                                            ? 'border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                                            : 'border-ds-border text-ds-text-muted hover:border-ds-text-muted'
                                    }`}
                                >
                                    {sort === 'top' ? 'Most Liked' : 'Recent'}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Agent Filter (for configs) */}
                    {(localFilters.type === 'all' ||
                        localFilters.type === 'configs') && (
                        <div>
                            <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                                Agent
                            </label>
                            <select
                                value={localFilters.agent ?? ''}
                                onChange={(e) =>
                                    setLocalFilters({
                                        ...localFilters,
                                        agent: e.target.value
                                            ? Number(e.target.value)
                                            : null,
                                    })
                                }
                                className="w-full border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary focus:border-ds-text-muted focus:outline-none"
                            >
                                <option value="">All Agents</option>
                                {agents.map((agent) => (
                                    <option key={agent.id} value={agent.id}>
                                        {agent.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}

                    {/* Config Type Filter (for configs) */}
                    {(localFilters.type === 'all' ||
                        localFilters.type === 'configs') && (
                        <div>
                            <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                                Config Type
                            </label>
                            <select
                                value={localFilters.config_type ?? ''}
                                onChange={(e) =>
                                    setLocalFilters({
                                        ...localFilters,
                                        config_type: e.target.value
                                            ? Number(e.target.value)
                                            : null,
                                        category: null, // Reset category when config type changes
                                    })
                                }
                                className="w-full border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary focus:border-ds-text-muted focus:outline-none"
                            >
                                <option value="">All Config Types</option>
                                {configTypes.map((ct) => (
                                    <option key={ct.id} value={ct.id}>
                                        {ct.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}

                    {/* Category Filter (for configs) */}
                    {(localFilters.type === 'all' ||
                        localFilters.type === 'configs') &&
                        filteredCategories.length > 0 && (
                            <div>
                                <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                                    Category
                                </label>
                                <select
                                    value={localFilters.category ?? ''}
                                    onChange={(e) =>
                                        setLocalFilters({
                                            ...localFilters,
                                            category: e.target.value
                                                ? Number(e.target.value)
                                                : null,
                                        })
                                    }
                                    className="w-full border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary focus:border-ds-text-muted focus:outline-none"
                                >
                                    <option value="">All Categories</option>
                                    {filteredCategories.map((cat) => (
                                        <option key={cat.id} value={cat.id}>
                                            {cat.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}

                    {/* Prompt Category Filter (for prompts) */}
                    {(localFilters.type === 'all' ||
                        localFilters.type === 'prompts') && (
                        <div>
                            <label className="mb-2 block text-xs text-ds-text-muted uppercase">
                                Prompt Category
                            </label>
                            <select
                                value={localFilters.prompt_category ?? ''}
                                onChange={(e) =>
                                    setLocalFilters({
                                        ...localFilters,
                                        prompt_category: e.target.value || null,
                                    })
                                }
                                className="w-full border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary focus:border-ds-text-muted focus:outline-none"
                            >
                                <option value="">All Categories</option>
                                {promptCategories.map((cat) => (
                                    <option key={cat} value={cat}>
                                        {cat.charAt(0).toUpperCase() +
                                            cat.slice(1)}
                                    </option>
                                ))}
                            </select>
                        </div>
                    )}
                </div>

                {/* Actions */}
                <div className="mt-8 flex gap-3">
                    <button
                        onClick={handleReset}
                        className="flex-1 border-2 border-ds-border px-4 py-2 text-sm text-ds-text-muted uppercase transition-colors hover:border-ds-text-muted hover:text-ds-text-primary"
                    >
                        Reset
                    </button>
                    <button
                        onClick={handleApply}
                        className="flex-1 border-2 border-ds-text-primary bg-ds-text-primary px-4 py-2 text-sm text-ds-bg-base uppercase transition-colors hover:bg-ds-text-secondary"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    );
}

export default function SearchPage({
    query,
    filters,
    results,
    counts,
    agents,
    configTypes,
    categories,
    promptCategories,
}: SearchPageProps) {
    const [searchInput, setSearchInput] = useState(query);
    const [isFilterOpen, setIsFilterOpen] = useState(false);

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get(
            search.url(),
            {
                q: searchInput,
                type: filters.type,
                agent: filters.agent,
                config_type: filters.config_type,
                category: filters.category,
                prompt_category: filters.prompt_category,
                sort: filters.sort,
            },
            { preserveState: true },
        );
    };

    const handleFilterApply = (
        newFilters: Partial<SearchPageProps['filters']>,
    ) => {
        router.get(
            search.url(),
            {
                q: query,
                ...newFilters,
            },
            { preserveState: true },
        );
    };

    const activeFilterCount = [
        filters.agent,
        filters.config_type,
        filters.category,
        filters.prompt_category,
        filters.type !== 'all' ? filters.type : null,
        filters.sort !== 'recent' ? filters.sort : null,
    ].filter(Boolean).length;

    return (
        <>
            <SeoHead
                title={query ? `Search: ${query}` : 'Search'}
                description="Search results for CLI AI agent configurations, MCP servers, and prompts."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Search Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6">
                            <form
                                onSubmit={handleSearch}
                                className="flex gap-3"
                            >
                                <div className="flex flex-1 border-2 border-ds-border bg-ds-bg-card focus-within:border-ds-text-muted">
                                    <input
                                        type="text"
                                        value={searchInput}
                                        onChange={(e) =>
                                            setSearchInput(e.target.value)
                                        }
                                        placeholder="Search configs, MCP servers, prompts..."
                                        className="flex-1 bg-transparent px-4 py-3 text-ds-text-primary placeholder-ds-text-muted focus:outline-none"
                                        autoFocus
                                    />
                                    <button
                                        type="submit"
                                        className="border-l-2 border-ds-border px-4 text-ds-text-muted transition-colors hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                    >
                                        <Search className="h-5 w-5" />
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setIsFilterOpen(true)}
                                    className="relative flex items-center gap-2 border-2 border-ds-border px-4 py-3 text-ds-text-muted transition-colors hover:border-ds-text-muted hover:text-ds-text-primary"
                                >
                                    <Filter className="h-5 w-5" />
                                    <span className="hidden text-sm uppercase sm:inline">
                                        Filters
                                    </span>
                                    {activeFilterCount > 0 && (
                                        <span className="absolute -top-2 -right-2 flex h-5 w-5 items-center justify-center bg-ds-text-primary text-xs text-ds-bg-base">
                                            {activeFilterCount}
                                        </span>
                                    )}
                                </button>
                            </form>

                            {/* Active Filters Display */}
                            {activeFilterCount > 0 && (
                                <div className="mt-4 flex flex-wrap items-center gap-2">
                                    <span className="text-xs text-ds-text-muted uppercase">
                                        Active:
                                    </span>
                                    {filters.type !== 'all' && (
                                        <span className="border border-ds-border bg-ds-bg-secondary px-2 py-1 text-xs text-ds-text-secondary">
                                            {filters.type}
                                        </span>
                                    )}
                                    {filters.sort !== 'recent' && (
                                        <span className="border border-ds-border bg-ds-bg-secondary px-2 py-1 text-xs text-ds-text-secondary">
                                            Most Liked
                                        </span>
                                    )}
                                    {filters.agent && (
                                        <span className="border border-ds-border bg-ds-bg-secondary px-2 py-1 text-xs text-ds-text-secondary">
                                            {agents.find(
                                                (a) =>
                                                    a.id ===
                                                    Number(filters.agent),
                                            )?.name || 'Agent'}
                                        </span>
                                    )}
                                    {filters.config_type && (
                                        <span className="border border-ds-border bg-ds-bg-secondary px-2 py-1 text-xs text-ds-text-secondary">
                                            {configTypes.find(
                                                (ct) =>
                                                    ct.id ===
                                                    Number(filters.config_type),
                                            )?.name || 'Config Type'}
                                        </span>
                                    )}
                                    {filters.prompt_category && (
                                        <span className="border border-ds-border bg-ds-bg-secondary px-2 py-1 text-xs text-ds-text-secondary">
                                            {filters.prompt_category}
                                        </span>
                                    )}
                                    <button
                                        onClick={() =>
                                            handleFilterApply({
                                                type: 'all',
                                                agent: null,
                                                config_type: null,
                                                category: null,
                                                prompt_category: null,
                                                sort: 'recent',
                                            })
                                        }
                                        className="text-xs text-ds-text-muted uppercase hover:text-ds-text-primary"
                                    >
                                        Clear all
                                    </button>
                                </div>
                            )}
                        </div>
                    </section>

                    {/* Results Summary */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-4 md:px-6">
                            <div className="flex flex-wrap items-center gap-4">
                                {query && (
                                    <span className="text-sm text-ds-text-secondary">
                                        {counts.total} results for &quot;
                                        <span className="text-ds-text-primary">
                                            {query}
                                        </span>
                                        &quot;
                                    </span>
                                )}
                                <div className="flex gap-4 text-xs text-ds-text-muted">
                                    {(filters.type === 'all' ||
                                        filters.type === 'configs') && (
                                        <span>
                                            {counts.configs} config
                                            {counts.configs !== 1 ? 's' : ''}
                                        </span>
                                    )}
                                    {(filters.type === 'all' ||
                                        filters.type === 'mcp-servers') && (
                                        <span>
                                            {counts.mcpServers} MCP server
                                            {counts.mcpServers !== 1 ? 's' : ''}
                                        </span>
                                    )}
                                    {(filters.type === 'all' ||
                                        filters.type === 'prompts') && (
                                        <span>
                                            {counts.prompts} prompt
                                            {counts.prompts !== 1 ? 's' : ''}
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Results */}
                    {counts.total === 0 ? (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-16 text-center md:px-6">
                                <div className="text-ds-text-muted">
                                    <Search className="mx-auto mb-4 h-12 w-12" />
                                    <h2 className="mb-2 text-lg font-medium text-ds-text-primary uppercase">
                                        No Results Found
                                    </h2>
                                    <p className="text-sm">
                                        {query
                                            ? `No results found for "${query}". Try a different search term or adjust your filters.`
                                            : 'Enter a search term to find configs, MCP servers, and prompts.'}
                                    </p>
                                </div>
                            </div>
                        </section>
                    ) : (
                        <>
                            {/* Configs Results */}
                            {(filters.type === 'all' ||
                                filters.type === 'configs') &&
                                results.configs.length > 0 && (
                                    <section className="border-b-2 border-ds-border">
                                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                                Configs ({counts.configs})
                                            </h2>
                                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                                {results.configs.map(
                                                    (config) => (
                                                        <ConfigCard
                                                            key={config.id}
                                                            config={config}
                                                        />
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    </section>
                                )}

                            {/* MCP Servers Results */}
                            {(filters.type === 'all' ||
                                filters.type === 'mcp-servers') &&
                                results.mcpServers.length > 0 && (
                                    <section className="border-b-2 border-ds-border">
                                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                                MCP Servers ({counts.mcpServers}
                                                )
                                            </h2>
                                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                                {results.mcpServers.map(
                                                    (mcpServer) => (
                                                        <McpServerCard
                                                            key={mcpServer.id}
                                                            mcpServer={
                                                                mcpServer
                                                            }
                                                        />
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    </section>
                                )}

                            {/* Prompts Results */}
                            {(filters.type === 'all' ||
                                filters.type === 'prompts') &&
                                results.prompts.length > 0 && (
                                    <section className="border-ds-border">
                                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                                Prompts ({counts.prompts})
                                            </h2>
                                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                                {results.prompts.map(
                                                    (prompt) => (
                                                        <PromptCard
                                                            key={prompt.id}
                                                            prompt={prompt}
                                                        />
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    </section>
                                )}
                        </>
                    )}
                </main>

                <SiteFooter />
            </div>

            {/* Filter Modal */}
            <FilterModal
                isOpen={isFilterOpen}
                onClose={() => setIsFilterOpen(false)}
                filters={filters}
                agents={agents}
                configTypes={configTypes}
                categories={categories}
                promptCategories={promptCategories}
                onApply={handleFilterApply}
            />
        </>
    );
}
