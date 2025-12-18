import { show as showAgent } from '@/actions/App/Http/Controllers/AgentController';
import type { Agent } from '@/types/models';
import { Link } from '@inertiajs/react';

interface AgentCardProps {
    agent: Agent;
}

export function AgentCard({ agent }: AgentCardProps) {
    return (
        <Link
            href={showAgent(agent.slug)}
            className="group flex flex-col items-center gap-3 border-2 border-ds-border bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex h-12 w-12 items-center justify-center rounded bg-ds-bg-secondary text-ds-text-muted">
                {agent.logo ? (
                    <img
                        src={agent.logo}
                        alt={agent.name}
                        className="h-8 w-8"
                    />
                ) : (
                    <span className="text-lg font-medium">
                        {agent.name.charAt(0).toUpperCase()}
                    </span>
                )}
            </div>
            <div className="text-center">
                <div className="text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                    {agent.name}
                </div>
                <div className="text-xs text-ds-text-muted">
                    {agent.configs_count ?? 0} configs
                </div>
            </div>
        </Link>
    );
}
