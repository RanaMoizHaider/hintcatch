import {
    createConfig,
    createMcpServer,
    createPrompt,
    createSkill,
} from '@/actions/App/Http/Controllers/SubmitController';
import { show as showUserProfile } from '@/actions/App/Http/Controllers/UserProfileController';
import AppLayout from '@/layouts/app-layout';
import { SharedData } from '@/types';
import { Deferred, Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    ArrowUp,
    FileCode,
    Heart,
    MessageSquare,
    Plus,
    Server,
    Sparkles,
} from 'lucide-react';

interface RecentItem {
    id: number;
    title?: string;
    name?: string;
    slug: string;
    created_at: string;
}

interface DashboardProps {
    stats?: {
        totalConfigs: number;
        totalMcpServers: number;
        totalPrompts: number;
        totalSkills: number;
        totalFavorites: number;
        totalUpvotesReceived: number;
    };
    recentConfigs: RecentItem[];
    recentMcpServers: RecentItem[];
    recentPrompts: RecentItem[];
    recentSkills: RecentItem[];
}

function StatsGrid({
    stats,
    profileUrl,
}: {
    stats: DashboardProps['stats'];
    profileUrl: string;
}) {
    return (
        <div className="grid grid-cols-2 gap-4 md:grid-cols-6">
            <Link
                href={profileUrl}
                className="hover:border-ds-accent border-2 border-ds-border bg-ds-bg-card p-4 text-center transition-colors"
            >
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <FileCode className="h-4 w-4" />
                    <span className="text-xs uppercase">Configs</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalConfigs}
                </div>
            </Link>
            <Link
                href={profileUrl}
                className="hover:border-ds-accent border-2 border-ds-border bg-ds-bg-card p-4 text-center transition-colors"
            >
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <Server className="h-4 w-4" />
                    <span className="text-xs uppercase">MCP Servers</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalMcpServers}
                </div>
            </Link>
            <Link
                href={profileUrl}
                className="hover:border-ds-accent border-2 border-ds-border bg-ds-bg-card p-4 text-center transition-colors"
            >
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <MessageSquare className="h-4 w-4" />
                    <span className="text-xs uppercase">Prompts</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalPrompts}
                </div>
            </Link>
            <Link
                href={profileUrl}
                className="hover:border-ds-accent border-2 border-ds-border bg-ds-bg-card p-4 text-center transition-colors"
            >
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <Sparkles className="h-4 w-4" />
                    <span className="text-xs uppercase">Skills</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalSkills}
                </div>
            </Link>
            <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <Heart className="h-4 w-4" />
                    <span className="text-xs uppercase">Favorites</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalFavorites}
                </div>
            </div>
            <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                    <ArrowUp className="h-4 w-4" />
                    <span className="text-xs uppercase">Upvotes</span>
                </div>
                <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                    {stats?.totalUpvotesReceived}
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({
    stats,
    recentConfigs,
    recentMcpServers,
    recentPrompts,
    recentSkills,
}: DashboardProps) {
    const { auth } = usePage<SharedData>().props;
    const profileUrl = showUserProfile.url({
        user: auth.user.username as string,
    });

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-8">
                <div>
                    <h1 className="text-2xl font-semibold text-ds-text-primary">
                        Dashboard
                    </h1>
                    <p className="mt-1 text-sm text-ds-text-muted">
                        Welcome back! Here's an overview of your activity.
                    </p>
                </div>

                <Deferred
                    data="stats"
                    fallback={
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-6">
                            {[...Array(6)].map((_, i) => (
                                <div
                                    key={i}
                                    className="animate-pulse border-2 border-ds-border bg-ds-bg-card p-4 text-center"
                                >
                                    <div className="flex items-center justify-center gap-2">
                                        <div className="h-4 w-4 rounded bg-ds-border" />
                                        <div className="h-3 w-16 rounded bg-ds-border" />
                                    </div>
                                    <div className="mx-auto mt-2 h-8 w-8 rounded bg-ds-border" />
                                </div>
                            ))}
                        </div>
                    }
                >
                    <StatsGrid stats={stats!} profileUrl={profileUrl} />
                </Deferred>

                <div>
                    <h2 className="mb-4 text-sm font-medium text-ds-text-muted uppercase">
                        Quick Actions
                    </h2>
                    <div className="grid gap-4 md:grid-cols-4">
                        <Link
                            href={createConfig.url()}
                            className="group flex items-center justify-between border-2 border-ds-border p-4 transition-colors hover:bg-ds-bg-card"
                        >
                            <div className="flex items-center gap-3">
                                <Plus className="h-5 w-5 text-ds-text-muted" />
                                <div>
                                    <h3 className="text-sm font-medium text-ds-text-primary">
                                        New Config
                                    </h3>
                                    <p className="text-xs text-ds-text-muted">
                                        Share your agent configuration
                                    </p>
                                </div>
                            </div>
                            <ArrowRight className="h-4 w-4 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                        </Link>
                        <Link
                            href={createMcpServer.url()}
                            className="group flex items-center justify-between border-2 border-ds-border p-4 transition-colors hover:bg-ds-bg-card"
                        >
                            <div className="flex items-center gap-3">
                                <Plus className="h-5 w-5 text-ds-text-muted" />
                                <div>
                                    <h3 className="text-sm font-medium text-ds-text-primary">
                                        New MCP Server
                                    </h3>
                                    <p className="text-xs text-ds-text-muted">
                                        Add an MCP server
                                    </p>
                                </div>
                            </div>
                            <ArrowRight className="h-4 w-4 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                        </Link>
                        <Link
                            href={createPrompt.url()}
                            className="group flex items-center justify-between border-2 border-ds-border p-4 transition-colors hover:bg-ds-bg-card"
                        >
                            <div className="flex items-center gap-3">
                                <Plus className="h-5 w-5 text-ds-text-muted" />
                                <div>
                                    <h3 className="text-sm font-medium text-ds-text-primary">
                                        New Prompt
                                    </h3>
                                    <p className="text-xs text-ds-text-muted">
                                        Create a new prompt
                                    </p>
                                </div>
                            </div>
                            <ArrowRight className="h-4 w-4 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                        </Link>
                        <Link
                            href={createSkill.url()}
                            className="group flex items-center justify-between border-2 border-ds-border p-4 transition-colors hover:bg-ds-bg-card"
                        >
                            <div className="flex items-center gap-3">
                                <Plus className="h-5 w-5 text-ds-text-muted" />
                                <div>
                                    <h3 className="text-sm font-medium text-ds-text-primary">
                                        New Skill
                                    </h3>
                                    <p className="text-xs text-ds-text-muted">
                                        Create a new agent skill
                                    </p>
                                </div>
                            </div>
                            <ArrowRight className="h-4 w-4 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-4">
                    <div>
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                Recent Configs
                            </h2>
                            {recentConfigs.length > 0 && (
                                <Link
                                    href={profileUrl}
                                    className="text-ds-accent text-xs hover:underline"
                                >
                                    View all
                                </Link>
                            )}
                        </div>
                        <div className="space-y-2">
                            {recentConfigs.length > 0 ? (
                                recentConfigs.map((config) => (
                                    <Link
                                        key={config.id}
                                        href={`/c/${config.slug}`}
                                        className="block border-2 border-ds-border p-3 transition-colors hover:bg-ds-bg-card"
                                    >
                                        <div className="text-sm font-medium text-ds-text-primary">
                                            {config.title || config.name}
                                        </div>
                                        <div className="mt-1 text-xs text-ds-text-muted">
                                            {new Date(
                                                config.created_at,
                                            ).toLocaleDateString()}
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                <div className="border-2 border-dashed border-ds-border p-4 text-center">
                                    <p className="text-sm text-ds-text-muted">
                                        No configs yet
                                    </p>
                                    <Link
                                        href={createConfig.url()}
                                        className="text-ds-accent mt-2 inline-block text-xs hover:underline"
                                    >
                                        Create your first config
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                Recent MCP Servers
                            </h2>
                            {recentMcpServers.length > 0 && (
                                <Link
                                    href={profileUrl}
                                    className="text-ds-accent text-xs hover:underline"
                                >
                                    View all
                                </Link>
                            )}
                        </div>
                        <div className="space-y-2">
                            {recentMcpServers.length > 0 ? (
                                recentMcpServers.map((server) => (
                                    <Link
                                        key={server.id}
                                        href={`/mcps/${server.slug}`}
                                        className="block border-2 border-ds-border p-3 transition-colors hover:bg-ds-bg-card"
                                    >
                                        <div className="text-sm font-medium text-ds-text-primary">
                                            {server.name}
                                        </div>
                                        <div className="mt-1 text-xs text-ds-text-muted">
                                            {new Date(
                                                server.created_at,
                                            ).toLocaleDateString()}
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                <div className="border-2 border-dashed border-ds-border p-4 text-center">
                                    <p className="text-sm text-ds-text-muted">
                                        No MCP servers yet
                                    </p>
                                    <Link
                                        href={createMcpServer.url()}
                                        className="text-ds-accent mt-2 inline-block text-xs hover:underline"
                                    >
                                        Add your first server
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                Recent Prompts
                            </h2>
                            {recentPrompts.length > 0 && (
                                <Link
                                    href={profileUrl}
                                    className="text-ds-accent text-xs hover:underline"
                                >
                                    View all
                                </Link>
                            )}
                        </div>
                        <div className="space-y-2">
                            {recentPrompts.length > 0 ? (
                                recentPrompts.map((prompt) => (
                                    <Link
                                        key={prompt.id}
                                        href={`/prompts/${prompt.slug}`}
                                        className="block border-2 border-ds-border p-3 transition-colors hover:bg-ds-bg-card"
                                    >
                                        <div className="text-sm font-medium text-ds-text-primary">
                                            {prompt.title || prompt.name}
                                        </div>
                                        <div className="mt-1 text-xs text-ds-text-muted">
                                            {new Date(
                                                prompt.created_at,
                                            ).toLocaleDateString()}
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                <div className="border-2 border-dashed border-ds-border p-4 text-center">
                                    <p className="text-sm text-ds-text-muted">
                                        No prompts yet
                                    </p>
                                    <Link
                                        href={createPrompt.url()}
                                        className="text-ds-accent mt-2 inline-block text-xs hover:underline"
                                    >
                                        Create your first prompt
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                Recent Skills
                            </h2>
                            {recentSkills.length > 0 && (
                                <Link
                                    href={profileUrl}
                                    className="text-ds-accent text-xs hover:underline"
                                >
                                    View all
                                </Link>
                            )}
                        </div>
                        <div className="space-y-2">
                            {recentSkills.length > 0 ? (
                                recentSkills.map((skill) => (
                                    <Link
                                        key={skill.id}
                                        href={`/skills/${skill.slug}`}
                                        className="block border-2 border-ds-border p-3 transition-colors hover:bg-ds-bg-card"
                                    >
                                        <div className="text-sm font-medium text-ds-text-primary">
                                            {skill.name}
                                        </div>
                                        <div className="mt-1 text-xs text-ds-text-muted">
                                            {new Date(
                                                skill.created_at,
                                            ).toLocaleDateString()}
                                        </div>
                                    </Link>
                                ))
                            ) : (
                                <div className="border-2 border-dashed border-ds-border p-4 text-center">
                                    <p className="text-sm text-ds-text-muted">
                                        No skills yet
                                    </p>
                                    <Link
                                        href={createSkill.url()}
                                        className="text-ds-accent mt-2 inline-block text-xs hover:underline"
                                    >
                                        Create your first skill
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
