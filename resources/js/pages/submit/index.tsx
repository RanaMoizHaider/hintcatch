import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import type { SubmitIndexPageProps } from '@/types/models';
import { Head, Link } from '@inertiajs/react';
import { FileCode, MessageSquare, Server, Sparkles } from 'lucide-react';

export default function SubmitIndex({}: SubmitIndexPageProps) {
    const submissionTypes = [
        {
            title: 'Config',
            description:
                'Share configuration files for AI agents like rules, settings, or MCP configurations.',
            icon: FileCode,
            href: '/submit/config',
            examples: ['AGENTS.md', 'claude_rules', 'MCP configs', 'Settings'],
        },
        {
            title: 'MCP Server',
            description:
                'Add a Model Context Protocol server that extends AI agent capabilities.',
            icon: Server,
            href: '/submit/mcp-server',
            examples: ['Remote servers', 'Local commands', 'API integrations'],
        },
        {
            title: 'Agent Skill',
            description:
                'Share reusable skills that teach AI agents new capabilities and workflows.',
            icon: Sparkles,
            href: '/submit/skill',
            examples: ['Code patterns', 'Workflows', 'Domain expertise'],
        },
        {
            title: 'Prompt',
            description:
                'Share reusable prompts for system instructions, tasks, reviews, and more.',
            icon: MessageSquare,
            href: '/submit/prompt',
            examples: ['System prompts', 'Task templates', 'Review guidelines'],
        },
    ];

    return (
        <>
            <Head title="Submit" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                Submit
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Share your configurations, MCP servers, prompts,
                                and skills with the community
                            </p>
                        </div>
                    </section>

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h2 className="mb-6 text-sm font-medium text-ds-text-muted uppercase">
                                Choose what to submit
                            </h2>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                                {submissionTypes.map((type) => (
                                    <Link
                                        key={type.title}
                                        href={type.href}
                                        className="group border-2 border-ds-border bg-ds-bg-card p-6 transition-colors hover:border-ds-text-muted"
                                    >
                                        <div className="mb-4 flex h-12 w-12 items-center justify-center border-2 border-ds-border bg-ds-bg-secondary">
                                            <type.icon className="h-6 w-6 text-ds-text-primary" />
                                        </div>
                                        <h3 className="mb-2 text-lg font-medium text-ds-text-primary uppercase">
                                            {type.title}
                                        </h3>
                                        <p className="mb-4 text-sm text-ds-text-secondary">
                                            {type.description}
                                        </p>
                                        <div className="flex flex-wrap gap-2">
                                            {type.examples.map((example) => (
                                                <span
                                                    key={example}
                                                    className="border border-ds-border px-2 py-0.5 text-xs text-ds-text-muted"
                                                >
                                                    {example}
                                                </span>
                                            ))}
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
