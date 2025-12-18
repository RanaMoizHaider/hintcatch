import { show as showPrompt } from '@/actions/App/Http/Controllers/PromptController';
import { Badge } from '@/components/ui/badge';
import type { Prompt } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowUp, Download } from 'lucide-react';

interface PromptCardProps {
    prompt: Prompt;
}

export function PromptCard({ prompt }: PromptCardProps) {
    return (
        <Link
            href={showPrompt(prompt.slug)}
            className="group flex flex-col border-2 border-ds-border bg-ds-bg-card p-4 transition-colors hover:border-ds-text-muted"
        >
            <div className="flex items-start justify-between gap-2">
                <div className="min-w-0 flex-1">
                    <h3 className="truncate text-sm font-medium text-ds-text-primary group-hover:text-ds-text-secondary">
                        {prompt.name}
                    </h3>
                    {prompt.user && (
                        <div className="mt-1 text-xs text-ds-text-muted">
                            by {prompt.user.name}
                        </div>
                    )}
                </div>
                <div className="flex items-center gap-1 text-xs text-ds-text-muted">
                    <ArrowUp className="h-3 w-3" />
                    <span>{prompt.vote_score}</span>
                </div>
            </div>

            {prompt.description && (
                <p className="mt-2 line-clamp-2 text-xs text-ds-text-secondary">
                    {prompt.description}
                </p>
            )}

            <div className="mt-auto flex items-center gap-2 pt-3">
                {prompt.category && (
                    <Badge
                        variant="outline"
                        className="border-ds-border text-ds-text-muted capitalize"
                    >
                        {prompt.category}
                    </Badge>
                )}
                <div className="ml-auto flex items-center gap-1 text-xs text-ds-text-muted">
                    <Download className="h-3 w-3" />
                    <span>{prompt.downloads}</span>
                </div>
            </div>
        </Link>
    );
}
