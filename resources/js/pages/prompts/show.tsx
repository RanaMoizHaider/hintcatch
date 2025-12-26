import { CommentSection } from '@/components/comment-section';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import { ShowPageHeader } from '@/components/show-page-header';
import type { PromptShowPageProps } from '@/types/models';
import { Check, Copy, FileCode } from 'lucide-react';
import { useState } from 'react';

export default function PromptsShow({
    prompt,
    relatedPrompts,
    moreFromUser,
    comments,
    interaction,
}: PromptShowPageProps) {
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        try {
            await navigator.clipboard.writeText(prompt.content);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    return (
        <>
            <SeoHead title={prompt.name} description={prompt.description} />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <ShowPageHeader
                        type="prompt"
                        name={prompt.name}
                        description={prompt.description}
                        voteScore={prompt.vote_score}
                        userVote={interaction.user_vote}
                        votableId={prompt.id}
                        isFavorited={interaction.is_favorited}
                        favoritesCount={interaction.favorites_count}
                        submitterUser={prompt.submitter}
                        sourceAuthor={prompt.source_author}
                        githubUrl={prompt.github_url}
                        sourceUrl={prompt.source_url}
                        category={prompt.category}
                        icon={
                            <FileCode className="h-6 w-6 text-ds-text-muted" />
                        }
                    />

                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="border-2 border-ds-border bg-ds-bg-card">
                                <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                                    <span className="text-xs text-ds-text-muted uppercase">
                                        Prompt
                                    </span>
                                    <button
                                        onClick={handleCopy}
                                        className="flex cursor-pointer items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        {copied ? (
                                            <>
                                                <Check className="h-3 w-3" />
                                                Copied
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="h-3 w-3" />
                                                Copy
                                            </>
                                        )}
                                    </button>
                                </div>
                                <div className="p-4">
                                    <pre className="font-mono text-sm whitespace-pre-wrap text-ds-text-primary">
                                        {prompt.content}
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </section>

                    <CommentSection
                        commentableType="prompt"
                        commentableId={prompt.id}
                        comments={comments}
                    />

                    {relatedPrompts.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Similar Prompts
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    {relatedPrompts.map((p) => (
                                        <PromptCard key={p.id} prompt={p} />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {prompt.submitter?.name}
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    {moreFromUser.map((p) => (
                                        <PromptCard key={p.id} prompt={p} />
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
