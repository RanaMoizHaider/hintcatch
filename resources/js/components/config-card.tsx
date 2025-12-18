import { show as showConfig } from '@/actions/App/Http/Controllers/ConfigController';
import { Badge } from '@/components/ui/badge';
import type { Config } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp, Download } from 'lucide-react';

interface ConfigCardProps {
    config: Config;
    showAgent?: boolean;
}

export function ConfigCard({ config, showAgent = true }: ConfigCardProps) {
    return (
        <Link
            href={showConfig(config.slug)}
            className="group flex flex-col border-2 border-ds-border bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                        {config.name}
                    </h3>
                    {config.user && (
                        <div className="mt-1 text-xs text-ds-text-muted">
                            by {config.user.name}
                        </div>
                    )}
                </div>
                <div className="flex items-center gap-1 text-xs text-ds-text-muted">
                    <ArrowUp className="h-3 w-3" />
                    <span>{config.vote_score}</span>
                </div>
            </div>

            {config.description && (
                <p className="mt-2 line-clamp-2 text-xs text-ds-text-secondary">
                    {config.description}
                </p>
            )}

            <div className="mt-auto flex items-center gap-2 pt-3">
                {showAgent && config.agent && (
                    <Badge
                        variant="outline"
                        className="border-ds-border text-ds-text-muted"
                    >
                        {config.agent.name}
                    </Badge>
                )}
                {config.config_type && (
                    <Badge
                        variant="outline"
                        className="border-ds-border text-ds-text-muted"
                    >
                        {config.config_type.name}
                    </Badge>
                )}
                <div className="ml-auto flex items-center gap-1 text-xs text-ds-text-muted">
                    <Download className="h-3 w-3" />
                    <span>{config.downloads}</span>
                </div>
            </div>
        </Link>
    );
}
