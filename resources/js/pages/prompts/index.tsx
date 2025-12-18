import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import type { Prompt, PromptIndexPageProps } from '@/types/models';
import { useState } from 'react';

export default function PromptsIndex({
    prompts,
    featuredPrompts,
    categories,
}: PromptIndexPageProps) {
    const [activeCategory, setActiveCategory] = useState<string | null>(null);

    const promptList: Prompt[] = prompts.data;

    const filteredPrompts = activeCategory
        ? promptList.filter((p) => p.category === activeCategory)
        : promptList;

    const handleCategoryFilter = (category: string | null) => {
        setActiveCategory(category);
    };

    return (
        <>
            <SeoHead
                title="Prompts"
                description="Curated prompts to enhance your AI agent workflows."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                Prompts
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Curated prompts to enhance your AI agent
                                workflows
                            </p>
                        </div>
                    </section>

                    {/* Category Filter */}
                    {categories && categories.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-4 md:px-6">
                                <div className="flex flex-wrap items-center gap-2">
                                    <span className="text-xs text-ds-text-muted uppercase">
                                        Filter:
                                    </span>
                                    <button
                                        onClick={() =>
                                            handleCategoryFilter(null)
                                        }
                                        className={`cursor-pointer border-2 px-3 py-1 text-xs transition-colors ${
                                            activeCategory === null
                                                ? 'border-ds-text-primary bg-ds-text-primary text-ds-bg-base'
                                                : 'border-ds-border text-ds-text-secondary hover:border-ds-text-muted'
                                        }`}
                                    >
                                        All
                                    </button>
                                    {categories.map((category) => (
                                        <button
                                            key={category}
                                            onClick={() =>
                                                handleCategoryFilter(category)
                                            }
                                            className={`cursor-pointer border-2 px-3 py-1 text-xs capitalize transition-colors ${
                                                activeCategory === category
                                                    ? 'border-ds-text-primary bg-ds-text-primary text-ds-bg-base'
                                                    : 'border-ds-border text-ds-text-secondary hover:border-ds-text-muted'
                                            }`}
                                        >
                                            {category}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Featured Prompts */}
                    {featuredPrompts &&
                        featuredPrompts.length > 0 &&
                        !activeCategory && (
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

                    {/* All Prompts */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                {activeCategory
                                    ? `${activeCategory} Prompts`
                                    : 'All Prompts'}
                            </h2>
                            {filteredPrompts.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {filteredPrompts.map((prompt) => (
                                        <PromptCard
                                            key={prompt.id}
                                            prompt={prompt}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <div className="py-12 text-center text-ds-text-muted">
                                    No prompts found
                                    {activeCategory
                                        ? ` in ${activeCategory} category`
                                        : ''}
                                    . Be the first to share one!
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
