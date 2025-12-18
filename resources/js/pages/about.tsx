import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import { Icons } from '@/components/ui/icons';
import { GitPullRequest } from 'lucide-react';
import { useEffect, useState } from 'react';

interface Contributor {
    id: number;
    login: string;
    avatar_url: string;
    html_url: string;
    contributions: number;
}

interface AboutPageProps {
    stats: {
        totalConfigs: number;
        totalMcpServers: number;
        totalPrompts: number;
        totalContributors: number;
    };
}

const userRoles: Record<string, string> = {
    ranamoizhaider: 'Owner',
};

function getRole(username: string): string | undefined {
    return userRoles[username as keyof typeof userRoles];
}

export default function About({ stats }: AboutPageProps) {
    const [contributors, setContributors] = useState<Contributor[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        async function fetchContributors() {
            try {
                const response = await fetch(
                    'https://api.github.com/repos/ranamoizhaider/hintcatch/contributors',
                );
                if (!response.ok) {
                    throw new Error('Failed to fetch contributors');
                }
                const data = await response.json();
                setContributors(data);
            } catch (err) {
                setError(
                    err instanceof Error
                        ? err.message
                        : 'Failed to load contributors',
                );
            } finally {
                setLoading(false);
            }
        }

        fetchContributors();
    }, []);

    return (
        <>
            <SeoHead
                title="About"
                description="Learn about HintCatch - the directory for CLI AI agent configurations. Discover our mission to help developers find and share the best tools."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Hero Section */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-12 md:px-6 md:py-20">
                            <h1 className="text-3xl leading-tight font-normal tracking-tight text-ds-text-primary uppercase md:text-5xl">
                                About HintCatch
                            </h1>
                            <p className="mt-4 max-w-2xl text-lg text-ds-text-secondary">
                                The directory for CLI AI agent configurations.
                                Discover, share, and contribute rules, prompts,
                                and MCP server configs for the tools you love.
                            </p>
                        </div>
                    </section>

                    {/* Mission Section */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                                Our Mission
                            </h2>
                            <p className="max-w-3xl text-ds-text-secondary">
                                CLI AI agents like OpenCode, Claude Code,
                                Cursor, and others are transforming how
                                developers work. But finding the right
                                configurations, prompts, and extensions for
                                these tools can be challenging. HintCatch
                                aggregates and organizes these resources, making
                                it easy to discover what works best for your
                                workflow.
                            </p>
                        </div>
                    </section>

                    {/* Why HintCatch Section */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                Why HintCatch?
                            </h2>
                            <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div className="border-2 border-ds-border bg-ds-bg-card p-6">
                                    <h3 className="mb-2 text-lg font-medium text-ds-text-primary uppercase">
                                        Discover
                                    </h3>
                                    <p className="text-sm text-ds-text-secondary">
                                        Find configurations, MCP servers, and
                                        prompts shared by the community. No more
                                        searching through scattered GitHub repos
                                        and blog posts.
                                    </p>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-6">
                                    <h3 className="mb-2 text-lg font-medium text-ds-text-primary uppercase">
                                        Share
                                    </h3>
                                    <p className="text-sm text-ds-text-secondary">
                                        Contribute your own configurations and
                                        help others level up their development
                                        workflow. Get credit for your
                                        contributions.
                                    </p>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-6">
                                    <h3 className="mb-2 text-lg font-medium text-ds-text-primary uppercase">
                                        Connect
                                    </h3>
                                    <p className="text-sm text-ds-text-secondary">
                                        Join a community of developers who are
                                        pushing the boundaries of what&apos;s
                                        possible with AI-powered coding tools.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Stats Section */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto grid max-w-[1200px] grid-cols-2 md:grid-cols-4">
                            <div className="border-r-2 border-ds-border px-4 py-6 text-center md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalConfigs}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Configs
                                </div>
                            </div>
                            <div className="border-r-0 border-ds-border px-4 py-6 text-center md:border-r-2 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalMcpServers}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    MCP Servers
                                </div>
                            </div>
                            <div className="border-t-2 border-r-2 border-ds-border px-4 py-6 text-center md:border-t-0 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalPrompts}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Prompts
                                </div>
                            </div>
                            <div className="border-t-2 border-ds-border px-4 py-6 text-center md:border-t-0 md:px-6">
                                <div className="text-2xl font-medium text-ds-text-primary">
                                    {stats.totalContributors}
                                </div>
                                <div className="text-xs text-ds-text-muted uppercase">
                                    Contributors
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Contributors Section */}
                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="mb-6 flex items-center gap-3">
                                <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                    GitHub Contributors
                                </h2>
                                <a
                                    href="https://github.com/ranamoizhaider/hintcatch"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                >
                                    <Icons.github className="h-4 w-4" />
                                </a>
                            </div>

                            {loading ? (
                                <div className="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                                    {[...Array(5)].map((_, i) => (
                                        <div
                                            key={i}
                                            className="animate-pulse border-2 border-ds-border bg-ds-bg-card p-4"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="h-10 w-10 bg-ds-bg-secondary"></div>
                                                <div className="flex-1 space-y-2">
                                                    <div className="h-3 w-20 bg-ds-bg-secondary"></div>
                                                    <div className="h-2 w-12 bg-ds-bg-secondary"></div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : error ? (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-6 text-center">
                                    <p className="text-ds-text-muted">
                                        {error}
                                    </p>
                                </div>
                            ) : contributors.length > 0 ? (
                                <div className="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                                    {contributors.map((contributor) => (
                                        <a
                                            key={contributor.id}
                                            href={contributor.html_url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="group block border-2 border-ds-border bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
                                        >
                                            <div className="flex items-center gap-3">
                                                <img
                                                    src={contributor.avatar_url}
                                                    alt={`${contributor.login}'s avatar`}
                                                    className="h-10 w-10 border border-ds-border"
                                                />
                                                <div className="min-w-0 flex-1">
                                                    <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                                                        @{contributor.login}
                                                    </h3>
                                                    <div className="mt-1 flex items-center justify-between">
                                                        {getRole(
                                                            contributor.login,
                                                        ) ? (
                                                            <span className="bg-ds-bg-secondary px-2 py-0.5 text-xs text-ds-text-muted">
                                                                {getRole(
                                                                    contributor.login,
                                                                )}
                                                            </span>
                                                        ) : (
                                                            <span className="text-xs text-ds-text-muted">
                                                                Contributor
                                                            </span>
                                                        )}
                                                        <div className="flex items-center gap-1 text-xs text-ds-text-muted">
                                                            <GitPullRequest className="h-3 w-3" />
                                                            <span>
                                                                {
                                                                    contributor.contributions
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    ))}
                                </div>
                            ) : (
                                <div className="border-2 border-ds-border bg-ds-bg-card p-6 text-center">
                                    <p className="text-ds-text-muted">
                                        No contributors found
                                    </p>
                                </div>
                            )}

                            <div className="mt-8 border-t-2 border-ds-border pt-6 text-center">
                                <p className="text-ds-text-secondary">
                                    Thank you to everyone who contributes to
                                    HintCatch!
                                </p>
                                <p className="mt-2 text-sm text-ds-text-muted">
                                    Every contribution helps build a better
                                    resource for the community.
                                </p>
                            </div>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
