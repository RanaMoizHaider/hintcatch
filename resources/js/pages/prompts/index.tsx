import { index } from '@/actions/App/Http/Controllers/PromptController';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { PromptCard } from '@/components/prompt-card';
import { SearchInput } from '@/components/search-input';
import { SeoHead } from '@/components/seo-head';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { SharedData } from '@/types';
import type { Prompt, PromptIndexPageProps } from '@/types/models';
import { InfiniteScroll, Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface Props extends Omit<PromptIndexPageProps, 'prompts'> {
    prompts: {
        data: Prompt[];
        links: {
            next?: string;
        };
        meta: {
            current_page: number;
        };
    };
    filters: {
        search?: string;
        category?: string;
    };
}

export default function PromptsIndex({
    prompts,
    featuredPrompts,
    categories,
    filters = { search: '', category: '' },
}: Props) {
    const { auth } = usePage<SharedData>().props;
    const [search, setSearch] = useState(filters.search || '');
    const submitHref = auth.user ? '/submit' : login();

    const handleSearch = (value: string) => {
        setSearch(value);
        router.get(
            index.url(),
            {
                search: value || undefined,
                category: filters.category || undefined,
            },
            {
                preserveState: true,
                replace: true,
                reset: ['prompts'],
            },
        );
    };

    const handleCategoryChange = (category: string) => {
        router.get(
            index.url(),
            {
                search: search || undefined,
                category: category === filters.category ? undefined : category,
            },
            {
                preserveState: true,
                replace: true,
                reset: ['prompts'],
            },
        );
    };

    return (
        <>
            <SeoHead
                title="Prompts"
                description="System prompts and templates for your AI agents."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                        Prompts
                                    </h1>
                                    <p className="mt-2 text-ds-text-secondary">
                                        System prompts and templates for your AI
                                        agents
                                    </p>
                                </div>
                                <div className="w-full md:w-72">
                                    <SearchInput
                                        value={search}
                                        onChange={handleSearch}
                                        placeholder="Search prompts..."
                                    />
                                </div>
                            </div>

                            {categories.length > 0 && (
                                <div className="mt-6 flex flex-wrap gap-2">
                                    <button
                                        onClick={() => handleCategoryChange('')}
                                        className={`px-3 py-1 text-xs uppercase transition-colors ${
                                            !filters.category
                                                ? 'border-2 border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                                                : 'border-2 border-transparent text-ds-text-muted hover:text-ds-text-secondary'
                                        }`}
                                    >
                                        All Categories
                                    </button>
                                    {categories.map((category) => (
                                        <button
                                            key={category}
                                            onClick={() =>
                                                handleCategoryChange(category)
                                            }
                                            className={`px-3 py-1 text-xs uppercase transition-colors ${
                                                filters.category === category
                                                    ? 'border-2 border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                                                    : 'border-2 border-transparent text-ds-text-muted hover:text-ds-text-secondary'
                                            }`}
                                        >
                                            {category}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                    </section>

                    {!search &&
                        !filters.category &&
                        featuredPrompts &&
                        featuredPrompts.length > 0 && (
                            <section className="border-b-2 border-ds-border">
                                <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                    <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                        Featured
                                    </h2>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {featuredPrompts.map((prompt) => (
                                            <PromptCard
                                                key={prompt.id}
                                                prompt={prompt}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </section>
                        )}

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                {search || filters.category
                                    ? 'Search Results'
                                    : 'All Prompts'}
                            </h2>
                            {prompts.data.length > 0 ? (
                                <InfiniteScroll
                                    data="prompts"
                                    buffer={500}
                                    loading={
                                        <div className="mt-8 flex justify-center">
                                            <div className="h-6 w-6 animate-spin rounded-full border-2 border-ds-border border-t-ds-text-primary" />
                                        </div>
                                    }
                                >
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {prompts.data.map((prompt) => (
                                            <PromptCard
                                                key={prompt.id}
                                                prompt={prompt}
                                            />
                                        ))}
                                    </div>
                                </InfiniteScroll>
                            ) : (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-12 text-center">
                                    <p className="text-ds-text-muted">
                                        {search
                                            ? `No prompts found matching "${search}"`
                                            : 'No prompts yet. Be the first to share one!'}
                                    </p>
                                    {!search && !filters.category && (
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
