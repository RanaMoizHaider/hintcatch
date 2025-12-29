import { index } from '@/actions/App/Http/Controllers/SkillController';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SearchInput } from '@/components/search-input';
import { SeoHead } from '@/components/seo-head';
import { SkillCard } from '@/components/skill-card';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import { SharedData } from '@/types';
import type { PaginatedData, Skill } from '@/types/models';
import { InfiniteScroll, Link, router, usePage } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';

interface Props {
    skills: PaginatedData<Skill>;
    featuredSkills: Skill[];
    filters: {
        search?: string;
    };
}

export default function SkillsIndex({
    skills,
    featuredSkills,
    filters,
}: Props) {
    const { auth } = usePage<SharedData>().props;
    const [search, setSearch] = useState(filters.search || '');
    const submitHref = auth.user ? '/submit' : login();

    const handleSearch = (value: string) => {
        setSearch(value);
        router.get(
            index.url(),
            { search: value || undefined },
            {
                preserveState: true,
                replace: true,
                reset: ['skills'],
            },
        );
    };

    return (
        <>
            <SeoHead
                title="Agent Skills"
                description="Reusable skills and capabilities for AI coding agents following the agentskills.io specification."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                        Agent Skills
                                    </h1>
                                    <p className="mt-2 text-ds-text-secondary">
                                        Reusable skills and capabilities for AI
                                        coding agents
                                    </p>
                                </div>
                                <div className="w-full md:w-72">
                                    <SearchInput
                                        value={search}
                                        onChange={handleSearch}
                                        placeholder="Search skills..."
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {!search && featuredSkills && featuredSkills.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Featured
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {featuredSkills.map((skill) => (
                                        <SkillCard
                                            key={skill.id}
                                            skill={skill}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                {search ? 'Search Results' : 'All Skills'}
                            </h2>
                            {skills.data.length > 0 ? (
                                <InfiniteScroll
                                    data="skills"
                                    buffer={500}
                                    loading={
                                        <div className="mt-8 flex justify-center">
                                            <div className="h-6 w-6 animate-spin rounded-full border-2 border-ds-border border-t-ds-text-primary" />
                                        </div>
                                    }
                                >
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        {skills.data.map((skill) => (
                                            <SkillCard
                                                key={skill.id}
                                                skill={skill}
                                            />
                                        ))}
                                    </div>
                                </InfiniteScroll>
                            ) : (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-12 text-center">
                                    <p className="text-ds-text-muted">
                                        {search
                                            ? `No skills found matching "${search}"`
                                            : 'No skills yet. Be the first to share one!'}
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
