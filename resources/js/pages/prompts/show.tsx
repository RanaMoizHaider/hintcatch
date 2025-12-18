import { show as showUser } from '@/actions/App/Http/Controllers/UserProfileController';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useInitials } from '@/hooks/use-initials';
import type { PromptShowPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp, Check, Copy, Download, ExternalLink } from 'lucide-react';
import { useState } from 'react';

export default function PromptsShow({
    prompt,
    relatedPrompts,
    moreFromUser,
}: PromptShowPageProps) {
    const getInitials = useInitials();
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
                    {/* Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div className="flex-1">
                                    <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                        {prompt.name}
                                    </h1>
                                    {prompt.description && (
                                        <p className="mt-2 text-ds-text-secondary">
                                            {prompt.description}
                                        </p>
                                    )}
                                    <div className="mt-4 flex flex-wrap items-center gap-3">
                                        {prompt.category && (
                                            <Badge
                                                variant="outline"
                                                className="border-ds-border text-ds-text-secondary capitalize"
                                            >
                                                {prompt.category}
                                            </Badge>
                                        )}
                                    </div>
                                </div>
                                <div className="flex items-center gap-6 text-sm text-ds-text-muted">
                                    <div className="flex items-center gap-1">
                                        <ArrowUp className="h-4 w-4" />
                                        <span>{prompt.vote_score}</span>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <Download className="h-4 w-4" />
                                        <span>{prompt.downloads}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Author */}
                            {prompt.user && (
                                <div className="mt-6 flex items-center gap-3">
                                    <Link href={showUser(prompt.user.username)}>
                                        <Avatar className="h-8 w-8">
                                            <AvatarImage
                                                src={
                                                    prompt.user.avatar ??
                                                    undefined
                                                }
                                                alt={prompt.user.name}
                                            />
                                            <AvatarFallback className="bg-ds-bg-secondary text-xs text-ds-text-muted">
                                                {getInitials(prompt.user.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                    </Link>
                                    <div>
                                        <Link
                                            href={showUser(
                                                prompt.user.username,
                                            )}
                                            className="text-sm text-ds-text-primary hover:text-ds-text-secondary"
                                        >
                                            {prompt.user.name}
                                        </Link>
                                        <div className="text-xs text-ds-text-muted">
                                            @{prompt.user.username}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Source */}
                            {prompt.source_url && (
                                <div className="mt-4">
                                    <a
                                        href={prompt.source_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        <ExternalLink className="h-3 w-3" />
                                        Source
                                        {prompt.source_author &&
                                            ` by ${prompt.source_author}`}
                                    </a>
                                </div>
                            )}
                        </div>
                    </section>

                    {/* Prompt Content */}
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

                    {/* Related Prompts */}
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

                    {/* More from User */}
                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {prompt.user?.name}
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
