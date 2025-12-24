import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import { show as showConfigType } from '@/actions/App/Http/Controllers/ConfigTypeController';
import { show as showUser } from '@/actions/App/Http/Controllers/UserProfileController';
import { CodeViewer } from '@/components/code-viewer';
import { CommentSection } from '@/components/comment-section';
import { ConfigCard } from '@/components/config-card';
import { FavoriteButton } from '@/components/favorite-button';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useInitials } from '@/hooks/use-initials';
import type { ConfigShowPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp, Download, ExternalLink } from 'lucide-react';

export default function ConfigsShow({
    config,
    relatedConfigs,
    moreFromUser,
    comments,
    interaction,
}: ConfigShowPageProps) {
    const getInitials = useInitials();

    return (
        <>
            <SeoHead title={config.name} description={config.description} />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div className="flex-1">
                                    <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                        {config.name}
                                    </h1>
                                    {config.description && (
                                        <p className="mt-2 text-ds-text-secondary">
                                            {config.description}
                                        </p>
                                    )}
                                    <div className="mt-4 flex flex-wrap items-center gap-3">
                                        {config.agent && (
                                            <Link
                                                href={showAgent(
                                                    config.agent.slug,
                                                )}
                                            >
                                                <Badge
                                                    variant="outline"
                                                    className="border-ds-border text-ds-text-secondary hover:border-ds-text-muted"
                                                >
                                                    {config.agent.name}
                                                </Badge>
                                            </Link>
                                        )}
                                        {config.config_type && (
                                            <Link
                                                href={showConfigType(
                                                    config.config_type.slug,
                                                )}
                                            >
                                                <Badge
                                                    variant="outline"
                                                    className="border-ds-border text-ds-text-secondary hover:border-ds-text-muted"
                                                >
                                                    {config.config_type.name}
                                                </Badge>
                                            </Link>
                                        )}
                                        {config.category && (
                                            <Badge
                                                variant="outline"
                                                className="border-ds-border text-ds-text-muted"
                                            >
                                                {config.category.name}
                                            </Badge>
                                        )}
                                    </div>
                                </div>
                                <div className="flex items-center gap-6 text-sm text-ds-text-muted">
                                    <div className="flex items-center gap-1">
                                        <ArrowUp className="h-4 w-4" />
                                        <span>{config.vote_score}</span>
                                    </div>
                                    <FavoriteButton
                                        favorableType="config"
                                        favorableId={config.id}
                                        isFavorited={interaction.is_favorited}
                                        favoritesCount={
                                            interaction.favorites_count
                                        }
                                    />
                                    <div className="flex items-center gap-1">
                                        <Download className="h-4 w-4" />
                                        <span>{config.downloads}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Author */}
                            {config.user && (
                                <div className="mt-6 flex items-center gap-3">
                                    <Link href={showUser(config.user.username)}>
                                        <Avatar className="h-8 w-8">
                                            <AvatarImage
                                                src={
                                                    config.user.avatar ??
                                                    undefined
                                                }
                                                alt={config.user.name}
                                            />
                                            <AvatarFallback className="bg-ds-bg-secondary text-xs text-ds-text-muted">
                                                {getInitials(config.user.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                    </Link>
                                    <div>
                                        <Link
                                            href={showUser(
                                                config.user.username,
                                            )}
                                            className="text-sm text-ds-text-primary hover:text-ds-text-secondary"
                                        >
                                            {config.user.name}
                                        </Link>
                                        <div className="text-xs text-ds-text-muted">
                                            @{config.user.username}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Source */}
                            {config.source_url && (
                                <div className="mt-4">
                                    <a
                                        href={config.source_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center gap-1 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        <ExternalLink className="h-3 w-3" />
                                        Source
                                        {config.source_author &&
                                            ` by ${config.source_author}`}
                                    </a>
                                </div>
                            )}
                        </div>
                    </section>

                    {/* Code Viewer */}
                    {config.files && config.files.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <CodeViewer files={config.files} />
                            </div>
                        </section>
                    )}

                    {/* Comments */}
                    <CommentSection
                        commentableType="config"
                        commentableId={config.id}
                        comments={comments}
                    />

                    {/* Related Configs */}
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

                    {/* More from User */}
                    {moreFromUser && moreFromUser.length > 0 && (
                        <section className="border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    More from {config.user?.name}
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
