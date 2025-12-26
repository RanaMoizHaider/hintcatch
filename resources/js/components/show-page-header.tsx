import { FavoriteButton } from '@/components/favorite-button';
import { Badge } from '@/components/ui/badge';
import { VoteButton } from '@/components/vote-button';
import type { PublicUser } from '@/types/models';
import { Link } from '@inertiajs/react';
import { Download, ExternalLink, Github } from 'lucide-react';
import { SubmitterInfo } from './submitter-info';

interface ShowPageHeaderProps {
    type: 'config' | 'prompt' | 'mcp-server' | 'skill';
    name: string;
    description?: string | null;
    voteScore: number;
    userVote?: 1 | -1 | null | undefined;
    votableId: number;
    isFavorited: boolean;
    favoritesCount: number;
    submitterUser?: PublicUser;
    sourceAuthor?: string | null;
    githubUrl?: string | null;
    sourceUrl?: string | null;
    agent?: { name: string; slug: string } | null;
    configType?: { name: string; slug: string } | null;
    category?: string | { name: string } | null;
    license?: string | null;
    isFeatured?: boolean;
    icon?: React.ReactNode;
    downloads?: number;
    mcpServerType?: string;
}

export function ShowPageHeader({
    type,
    name,
    description,
    voteScore,
    userVote,
    votableId,
    isFavorited,
    favoritesCount,
    submitterUser,
    sourceAuthor,
    githubUrl,
    sourceUrl,
    agent,
    configType,
    category,
    license,
    isFeatured,
    icon,
    downloads,
    mcpServerType,
}: ShowPageHeaderProps) {
    const categoryName =
        typeof category === 'string' ? category : category?.name;

    return (
        <section className="border-b-2 border-ds-border">
            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div className="flex-1">
                        <div className="flex items-start gap-4">
                            {icon && (
                                <div className="flex h-12 w-12 items-center justify-center border-2 border-ds-border bg-ds-bg-card">
                                    {icon}
                                </div>
                            )}
                            <div className="min-w-0 flex-1">
                                <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                    {name}
                                </h1>
                                {description && (
                                    <p className="mt-2 text-ds-text-secondary">
                                        {description}
                                    </p>
                                )}
                                <div className="mt-4 flex flex-wrap items-center gap-2">
                                    {agent && (
                                        <Link href={`/agents/${agent.slug}`}>
                                            <Badge
                                                variant="outline"
                                                className="border-ds-border text-ds-text-secondary hover:border-ds-text-muted"
                                            >
                                                {agent.name}
                                            </Badge>
                                        </Link>
                                    )}
                                    {configType && (
                                        <Link
                                            href={`/config-types/${configType.slug}`}
                                        >
                                            <Badge
                                                variant="outline"
                                                className="border-ds-border text-ds-text-secondary hover:border-ds-text-muted"
                                            >
                                                {configType.name}
                                            </Badge>
                                        </Link>
                                    )}
                                    {categoryName && (
                                        <Badge
                                            variant="outline"
                                            className="border-ds-border text-ds-text-muted capitalize"
                                        >
                                            {categoryName}
                                        </Badge>
                                    )}
                                    {mcpServerType && (
                                        <Badge
                                            variant="outline"
                                            className="border-ds-border text-ds-text-secondary"
                                        >
                                            {mcpServerType}
                                        </Badge>
                                    )}
                                    {license && (
                                        <Badge
                                            variant="outline"
                                            className="border-ds-border text-ds-text-muted"
                                        >
                                            {license}
                                        </Badge>
                                    )}
                                    {isFeatured && (
                                        <Badge className="bg-ds-accent-primary text-white">
                                            Featured
                                        </Badge>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        <VoteButton
                            votableType={type}
                            votableId={votableId}
                            voteScore={voteScore}
                            userVote={userVote ?? null}
                        />
                        <FavoriteButton
                            favoritableType={type}
                            favoritableId={votableId}
                            isFavorited={isFavorited}
                            favoritesCount={favoritesCount}
                        />
                        {downloads !== undefined && (
                            <div className="flex h-8 items-center gap-1 rounded-md border border-ds-border px-2 text-ds-text-muted">
                                <Download className="h-4 w-4" />
                                <span className="text-sm">{downloads}</span>
                            </div>
                        )}
                    </div>
                </div>

                <div className="mt-6 flex items-center gap-2">
                    {githubUrl && (
                        <a
                            href={githubUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex h-8 w-8 items-center justify-center rounded-md border border-ds-border text-ds-text-muted transition-all hover:border-ds-border-hover hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                            aria-label="View on GitHub"
                        >
                            <Github className="h-4 w-4" />
                        </a>
                    )}
                    {sourceUrl && (
                        <a
                            href={sourceUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="flex h-8 w-8 items-center justify-center rounded-md border border-ds-border text-ds-text-muted transition-all hover:border-ds-border-hover hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                            aria-label="View Source"
                        >
                            <ExternalLink className="h-4 w-4" />
                        </a>
                    )}
                    <SubmitterInfo
                        user={submitterUser}
                        sourceAuthor={sourceAuthor}
                    />
                </div>
            </div>
        </section>
    );
}

export default ShowPageHeader;
