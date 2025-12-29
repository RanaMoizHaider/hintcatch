import { ConfigCard } from '@/components/config-card';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { McpServerCard } from '@/components/mcp-server-card';
import { PromptCard } from '@/components/prompt-card';
import { SeoHead } from '@/components/seo-head';
import { SkillCard } from '@/components/skill-card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import type { UserProfilePageProps } from '@/types/models';
import {
    ArrowUp,
    ExternalLink,
    FileCode,
    Github,
    MessageSquare,
    Server,
    Sparkles,
} from 'lucide-react';

export default function UsersShow({
    profileUser,
    configs,
    prompts,
    mcpServers,
    skills,
    stats,
}: UserProfilePageProps) {
    const getInitials = useInitials();

    return (
        <>
            <SeoHead
                title={profileUser.name}
                description={`Browse configurations and resources shared by ${profileUser.name}.`}
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    {/* Profile Header */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="flex flex-col gap-6 md:flex-row md:items-start">
                                <Avatar className="h-24 w-24 border-2 border-ds-border">
                                    <AvatarImage
                                        src={profileUser.avatar ?? undefined}
                                        alt={profileUser.name}
                                    />
                                    <AvatarFallback className="bg-ds-bg-secondary text-2xl text-ds-text-muted">
                                        {getInitials(profileUser.name)}
                                    </AvatarFallback>
                                </Avatar>
                                <div className="flex-1">
                                    <h1 className="text-2xl font-medium text-ds-text-primary md:text-3xl">
                                        {profileUser.name}
                                    </h1>
                                    <p className="mt-1 text-ds-text-muted">
                                        @{profileUser.username}
                                    </p>
                                    {profileUser.bio && (
                                        <p className="mt-3 text-ds-text-secondary">
                                            {profileUser.bio}
                                        </p>
                                    )}
                                    <div className="mt-4 flex flex-wrap items-center gap-4 text-sm text-ds-text-muted">
                                        {profileUser.website && (
                                            <a
                                                href={profileUser.website}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="flex items-center gap-1 transition-colors hover:text-ds-text-primary"
                                            >
                                                <ExternalLink className="h-3 w-3" />
                                                Website
                                            </a>
                                        )}
                                        {profileUser.github_username && (
                                            <a
                                                href={`https://github.com/${profileUser.github_username}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="flex items-center gap-1 transition-colors hover:text-ds-text-primary"
                                            >
                                                <Github className="h-3 w-3" />
                                                {profileUser.github_username}
                                            </a>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Stats */}
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-6 md:px-6">
                            <div className="grid grid-cols-2 gap-4 md:grid-cols-5">
                                <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                                    <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                                        <FileCode className="h-4 w-4" />
                                        <span className="text-xs uppercase">
                                            Configs
                                        </span>
                                    </div>
                                    <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                                        {stats.totalConfigs}
                                    </div>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                                    <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                                        <Server className="h-4 w-4" />
                                        <span className="text-xs uppercase">
                                            MCP Servers
                                        </span>
                                    </div>
                                    <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                                        {stats.totalMcpServers}
                                    </div>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                                    <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                                        <MessageSquare className="h-4 w-4" />
                                        <span className="text-xs uppercase">
                                            Prompts
                                        </span>
                                    </div>
                                    <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                                        {stats.totalPrompts}
                                    </div>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                                    <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                                        <Sparkles className="h-4 w-4" />
                                        <span className="text-xs uppercase">
                                            Skills
                                        </span>
                                    </div>
                                    <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                                        {stats.totalSkills}
                                    </div>
                                </div>
                                <div className="border-2 border-ds-border bg-ds-bg-card p-4 text-center">
                                    <div className="flex items-center justify-center gap-2 text-ds-text-muted">
                                        <ArrowUp className="h-4 w-4" />
                                        <span className="text-xs uppercase">
                                            Total Votes
                                        </span>
                                    </div>
                                    <div className="mt-1 text-2xl font-medium text-ds-text-primary">
                                        {stats.totalVotes}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Configs */}
                    {configs.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Configs
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {configs.map((config) => (
                                        <ConfigCard
                                            key={config.id}
                                            config={config}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* MCP Servers */}
                    {mcpServers.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    MCP Servers
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {mcpServers.map((server) => (
                                        <McpServerCard
                                            key={server.id}
                                            mcpServer={server}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Prompts */}
                    {prompts.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Prompts
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {prompts.map((prompt) => (
                                        <PromptCard
                                            key={prompt.id}
                                            prompt={prompt}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Skills */}
                    {skills.length > 0 && (
                        <section className="border-b-2 border-ds-border">
                            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                                <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                    Skills
                                </h2>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    {skills.map((skill) => (
                                        <SkillCard
                                            key={skill.id}
                                            skill={skill}
                                        />
                                    ))}
                                </div>
                            </div>
                        </section>
                    )}

                    {/* Empty State */}
                    {configs.length === 0 &&
                        mcpServers.length === 0 &&
                        prompts.length === 0 &&
                        skills.length === 0 && (
                            <section className="border-ds-border">
                                <div className="mx-auto max-w-[1200px] px-4 py-12 text-center md:px-6">
                                    <p className="text-ds-text-muted">
                                        {profileUser.name} hasn't shared
                                        anything yet.
                                    </p>
                                </div>
                            </section>
                        )}
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
