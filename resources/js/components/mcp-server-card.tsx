import { show as showMcpServer } from '@/actions/App/Http/Controllers/McpServerController';
import { Badge } from '@/components/ui/badge';
import type { McpServer } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp, Download, Globe, Terminal } from 'lucide-react';

interface McpServerCardProps {
    mcpServer: McpServer;
}

export function McpServerCard({ mcpServer }: McpServerCardProps) {
    return (
        <Link
            href={showMcpServer(mcpServer.slug)}
            className="group flex flex-col border-2 border-ds-border bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <div className="flex items-center gap-2">
                        {mcpServer.type === 'remote' ? (
                            <Globe className="h-4 w-4 text-ds-text-muted" />
                        ) : (
                            <Terminal className="h-4 w-4 text-ds-text-muted" />
                        )}
                        <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                            {mcpServer.name}
                        </h3>
                    </div>
                    {mcpServer.user && (
                        <div className="mt-1 text-xs text-ds-text-muted">
                            by {mcpServer.user.name}
                        </div>
                    )}
                </div>
                <div className="flex items-center gap-1 text-xs text-ds-text-muted">
                    <ArrowUp className="h-3 w-3" />
                    <span>{mcpServer.vote_score}</span>
                </div>
            </div>

            {mcpServer.description && (
                <p className="mt-2 line-clamp-2 text-xs text-ds-text-secondary">
                    {mcpServer.description}
                </p>
            )}

            <div className="mt-auto flex items-center gap-2 pt-3">
                <Badge
                    variant="outline"
                    className="border-ds-border text-ds-text-muted"
                >
                    {mcpServer.type}
                </Badge>
                <div className="ml-auto flex items-center gap-1 text-xs text-ds-text-muted">
                    <Download className="h-3 w-3" />
                    <span>{mcpServer.downloads}</span>
                </div>
            </div>
        </Link>
    );
}
