import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import { SkillCard } from '@/components/skill-card';
import type { SkillIndexPageProps } from '@/types/models';

export default function SkillsIndex({
    skills,
    featuredSkills,
}: SkillIndexPageProps) {
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
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                Agent Skills
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Reusable skills and capabilities for AI coding
                                agents
                            </p>
                        </div>
                    </section>

                    {featuredSkills && featuredSkills.length > 0 && (
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
                                All Skills
                            </h2>
                            {skills.length > 0 ? (
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {skills.map((skill) => (
                                        <SkillCard
                                            key={skill.id}
                                            skill={skill}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <div className="py-12 text-center text-ds-text-muted">
                                    No skills yet. Be the first to share one!
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
