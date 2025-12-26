import { show as showMcpServer } from '@/actions/App/Http/Controllers/McpServerController';
import { Badge } from '@/components/ui/badge';
import type { McpServer } from '@/types/models';
import { Link } from '@inertiajs/react';

interface McpServerCardProps {
    mcpServer: McpServer;
}

export function McpServerCard({ mcpServer }: McpServerCardProps) {
    return (
        <Link
            href={showMcpServer(mcpServer.slug)}
            className="group flex flex-col border-2 border-ds-bg-card bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                        {mcpServer.name}
                    </h3>
                    {mcpServer.submitter && (
                        <div className="text-xs text-ds-text-muted">
                            by {mcpServer.submitter.name}
                        </div>
                    )}
                </div>
                <Badge
                    variant="outline"
                    className="border-ds-border text-ds-text-muted"
                >
                    {mcpServer.type === 'remote' ? 'Remote' : 'Local'}
                </Badge>
            </div>

            {mcpServer.description && (
                <p className="mt-2 line-clamp-2 text-xs text-ds-text-secondary">
                    {mcpServer.description}
                </p>
            )}

            <div className="mt-auto pt-3" />
        </Link>
    );
}
