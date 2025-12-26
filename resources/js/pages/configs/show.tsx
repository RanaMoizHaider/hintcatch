import { CommentSection } from '@/components/comment-section';
import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import {
    MultiFileViewer,
    type MultiFileViewerFile,
} from '@/components/multi-file-viewer';
import { SeoHead } from '@/components/seo-head';
import { ShowPageHeader } from '@/components/show-page-header';
import type { ConfigShowPageProps } from '@/types/models';
import { FileCode } from 'lucide-react';
import { useMemo } from 'react';

export default function ConfigsShow({
    config,
    relatedConfigs,
    moreFromUser,
    comments,
    interaction,
}: ConfigShowPageProps) {
    const configFiles = useMemo((): MultiFileViewerFile[] => {
        if (!config.files || config.files.length === 0) return [];

        return config.files.map((file) => ({
            id: file.id,
            filename: file.filename,
            content: file.content,
            path: file.path,
            language: file.language,
            isPrimary: file.is_primary,
        }));
    }, [config.files]);

    return (
        <>
            <SeoHead title={config.name} description={config.description} />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <ShowPageHeader
                        type="config"
                        name={config.name}
                        description={config.description}
                        voteScore={config.vote_score}
                        userVote={interaction.user_vote}
                        votableId={config.id}
                        isFavorited={interaction.is_favorited}
                        favoritesCount={interaction.favorites_count}
                        submitterUser={config.submitter}
                        sourceAuthor={config.source_author}
                        githubUrl={config.github_url}
                        sourceUrl={config.source_url}
                        agent={config.agent}
                        configType={config.config_type}
                        category={config.category}
                        icon={
                            <FileCode className="h-6 w-6 text-ds-text-muted" />
                        }
                    />

                    {config.instructions && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-lg font-medium text-ds-text-primary">
                                    Instructions
                                </h2>
                                <div className="prose prose-neutral dark:prose-invert max-w-none">
                                    <div className="rounded-lg border border-ds-border bg-ds-bg-secondary p-6 whitespace-pre-wrap text-ds-text-secondary">
                                        {config.instructions}
                                    </div>
                                </div>
                            </div>
                        </section>
                    )}

                    {configFiles.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <MultiFileViewer files={configFiles} />
                            </div>
                        </section>
                    )}

                    <CommentSection
                        commentableType="config"
                        commentableId={config.id}
                        comments={comments}
                    />

                    {relatedConfigs.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Related Configs
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {relatedConfigs.map((c) => (
                                        <ConfigCard key={c.id} config={c} />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {config.submitter?.name}
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    {moreFromUser.map((c) => (
                                        <ConfigCard key={c.id} config={c} />
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
