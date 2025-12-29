import { CommentSection } from '@/components/comment-section';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import {
    MultiFileViewer,
    type MultiFileViewerFile,
} from '@/components/multi-file-viewer';
import { SeoHead } from '@/components/seo-head';
import { ShowPageHeader } from '@/components/show-page-header';
import { SkillCard } from '@/components/skill-card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { Agent, SkillShowPageProps } from '@/types/models';
import { BookOpen, Check, Copy } from 'lucide-react';
import { useMemo, useState } from 'react';

import { MarkdownRenderer } from '@/components/markdown-renderer';

export default function SkillShow({
    skill,
    agentIntegrations,
    moreFromUser,
    comments,
    interaction,
}: SkillShowPageProps) {
    const [copiedAgent, setCopiedAgent] = useState<string | null>(null);

    const copyToClipboard = (text: string, id: string) => {
        navigator.clipboard.writeText(text);
        setCopiedAgent(id);
        setTimeout(() => setCopiedAgent(null), 2000);
    };

    const agents = Object.values(agentIntegrations).map((ai) => ai.agent);

    const buildSkillFiles = useMemo(() => {
        return (skillMd: string): MultiFileViewerFile[] => {
            const files: MultiFileViewerFile[] = [];

            files.push({
                id: 'skill-md',
                filename: 'SKILL.md',
                content: skillMd,
                language: 'markdown',
                isPrimary: true,
            });

            skill.scripts?.forEach((script, idx) => {
                files.push({
                    id: `script-${idx}`,
                    filename: script.name,
                    content: script.content || '',
                    language: 'text',
                });
            });

            skill.references?.forEach((ref, idx) => {
                files.push({
                    id: `ref-${idx}`,
                    filename: ref.name,
                    content: ref.content || '',
                    language: 'markdown',
                });
            });

            return files;
        };
    }, [skill.scripts, skill.references]);

    return (
        <>
            <SeoHead
                title={skill.name}
                description={skill.description || `${skill.name} - Agent Skill`}
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <ShowPageHeader
                        type="skill"
                        name={skill.name}
                        description={skill.description}
                        voteScore={skill.vote_score}
                        userVote={interaction.user_vote}
                        votableId={skill.id}
                        isFavorited={interaction.is_favorited}
                        favoritesCount={interaction.favorites_count}
                        submitterUser={skill.submitter}
                        sourceAuthor={skill.source_author}
                        githubUrl={skill.github_url}
                        sourceUrl={skill.source_url}
                        category={skill.category}
                        license={skill.license}
                        isFeatured={skill.is_featured}
                        icon={
                            <BookOpen className="h-6 w-6 text-ds-text-muted" />
                        }
                    />

                    {skill.readme && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-lg font-medium text-ds-text-primary">
                                    README
                                </h2>
                                <div className="rounded-lg border border-ds-border bg-ds-bg-secondary p-6">
                                    <MarkdownRenderer content={skill.readme} />
                                </div>
                            </div>
                        </section>
                    )}

                    {agents.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-6 md:px-6">
                                <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                                    Agent Integration
                                </h2>
                                <Tabs
                                    defaultValue={agents[0]?.slug}
                                    className="w-full"
                                >
                                    <TabsList className="mb-4 h-auto flex-wrap justify-start gap-1 bg-transparent p-0">
                                        {agents.map((agent: Agent) => (
                                            <TabsTrigger
                                                key={agent.slug}
                                                value={agent.slug}
                                                className="border border-ds-border/50 bg-ds-bg-card px-3 py-1.5 text-sm data-[state=active]:border-2 data-[state=active]:border-ds-text-primary data-[state=active]:bg-ds-bg-elevated"
                                            >
                                                {agent.name}
                                            </TabsTrigger>
                                        ))}
                                    </TabsList>
                                    {agents.map((agent: Agent) => {
                                        const agentIntegration =
                                            agentIntegrations[agent.slug];
                                        if (!agentIntegration) return null;

                                        const { integration } =
                                            agentIntegration;

                                        const skillFiles = buildSkillFiles(
                                            integration.skill_md,
                                        );

                                        return (
                                            <TabsContent
                                                key={agent.slug}
                                                value={agent.slug}
                                                className="mt-0"
                                            >
                                                <div className="space-y-6">
                                                    {(integration.install_path ||
                                                        integration.project_path) && (
                                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                            {integration.install_path && (
                                                                <div>
                                                                    <div className="mb-2 text-xs font-medium text-ds-text-muted uppercase">
                                                                        Global
                                                                        Path
                                                                    </div>
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            copyToClipboard(
                                                                                integration.install_path!,
                                                                                `${agent.slug}-global`,
                                                                            )
                                                                        }
                                                                        className="flex w-full cursor-pointer items-center justify-between border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm transition-colors hover:border-ds-text-muted"
                                                                    >
                                                                        <code className="text-left break-all text-ds-text-primary">
                                                                            {
                                                                                integration.install_path
                                                                            }
                                                                        </code>
                                                                        <span className="ml-2 shrink-0 text-ds-text-muted">
                                                                            {copiedAgent ===
                                                                            `${agent.slug}-global` ? (
                                                                                <Check className="h-4 w-4" />
                                                                            ) : (
                                                                                <Copy className="h-4 w-4" />
                                                                            )}
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            )}
                                                            {integration.project_path && (
                                                                <div>
                                                                    <div className="mb-2 text-xs font-medium text-ds-text-muted uppercase">
                                                                        Project
                                                                        Path
                                                                    </div>
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            copyToClipboard(
                                                                                integration.project_path!,
                                                                                `${agent.slug}-project`,
                                                                            )
                                                                        }
                                                                        className="flex w-full cursor-pointer items-center justify-between border-2 border-ds-border bg-ds-bg-card px-3 py-2 text-sm transition-colors hover:border-ds-text-muted"
                                                                    >
                                                                        <code className="text-left break-all text-ds-text-primary">
                                                                            {
                                                                                integration.project_path
                                                                            }
                                                                        </code>
                                                                        <span className="ml-2 shrink-0 text-ds-text-muted">
                                                                            {copiedAgent ===
                                                                            `${agent.slug}-project` ? (
                                                                                <Check className="h-4 w-4" />
                                                                            ) : (
                                                                                <Copy className="h-4 w-4" />
                                                                            )}
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            )}
                                                        </div>
                                                    )}

                                                    <MultiFileViewer
                                                        files={skillFiles}
                                                    />
                                                </div>
                                            </TabsContent>
                                        );
                                    })}
                                </Tabs>
                            </div>
                        </section>
                    )}

                    <CommentSection
                        comments={comments}
                        commentableType="skill"
                        commentableId={skill.id}
                    />

                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-t-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {skill.submitter?.name}
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {moreFromUser.map((s) => (
                                        <SkillCard key={s.id} skill={s} />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
