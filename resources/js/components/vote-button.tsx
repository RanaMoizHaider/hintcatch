import { toggle } from '@/actions/App/Http/Controllers/VoteController';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { ChevronDown, ChevronUp } from 'lucide-react';
import { useState, useTransition } from 'react';

interface VoteButtonProps {
    votableType: 'config' | 'prompt' | 'mcp-server' | 'skill' | 'comment';
    votableId: number;
    voteScore: number;
    userVote: 1 | -1 | null;
    size?: 'sm' | 'md';
    className?: string;
}

export function VoteButton({
    votableType,
    votableId,
    voteScore: initialScore,
    userVote: initialUserVote,
    size = 'md',
    className,
}: VoteButtonProps) {
    const { auth } = usePage<SharedData>().props;
    const [score, setScore] = useState(initialScore);
    const [userVote, setUserVote] = useState(initialUserVote);
    const [isPending, startTransition] = useTransition();

    const handleVote = (value: 1 | -1) => {
        if (!auth.user) {
            router.get('/login');
            return;
        }

        const previousVote = userVote;
        const previousScore = score;

        let newScore = score;
        let newVote: 1 | -1 | null = value;

        if (userVote === value) {
            newVote = null;
            newScore -= value;
        } else if (userVote === null) {
            newScore += value;
        } else {
            newScore += 2 * value;
        }

        setScore(newScore);
        setUserVote(newVote);

        startTransition(() => {
            router.post(
                toggle.url(),
                {
                    votable_type: votableType,
                    votable_id: votableId,
                    value: value,
                },
                {
                    preserveScroll: true,
                    onError: () => {
                        setScore(previousScore);
                        setUserVote(previousVote);
                    },
                },
            );
        });
    };

    const isSmall = size === 'sm';

    return (
        <div
            className={cn(
                'flex items-center',
                isSmall ? 'gap-1.5' : 'gap-2',
                className,
            )}
        >
            <Button
                variant="ghost"
                size="icon"
                className={cn(
                    'h-8 w-8 rounded-md border transition-all',
                    userVote === 1
                        ? 'border-ds-text-primary bg-ds-bg-secondary/10 text-ds-text-primary'
                        : 'border-ds-border text-ds-text-muted hover:border-ds-border-hover hover:bg-ds-bg-secondary hover:text-ds-text-primary',
                    isSmall && 'h-6 w-6',
                )}
                onClick={() => handleVote(1)}
                aria-label="Upvote"
                disabled={isPending}
            >
                <ChevronUp
                    className={cn(
                        'transition-all',
                        isSmall ? 'h-4 w-4' : 'h-5 w-5',
                        userVote === 1 && 'fill-current stroke-[3px]',
                    )}
                />
            </Button>

            <span
                className={cn(
                    'min-w-[1.5ch] text-center leading-none font-medium tabular-nums',
                    userVote !== null
                        ? 'text-ds-text-primary'
                        : 'text-ds-text-muted',
                    isSmall ? 'text-xs' : 'text-sm',
                )}
            >
                {score}
            </span>

            <Button
                variant="ghost"
                size="icon"
                className={cn(
                    'h-8 w-8 rounded-md border transition-all',
                    userVote === -1
                        ? 'border-ds-text-primary bg-ds-bg-secondary/10 text-ds-text-primary'
                        : 'border-ds-border text-ds-text-muted hover:border-ds-border-hover hover:bg-ds-bg-secondary hover:text-ds-text-primary',
                    isSmall && 'h-6 w-6',
                )}
                onClick={() => handleVote(-1)}
                aria-label="Downvote"
                disabled={isPending}
            >
                <ChevronDown
                    className={cn(
                        'transition-all',
                        isSmall ? 'h-4 w-4' : 'h-5 w-5',
                        userVote === -1 && 'fill-current stroke-[3px]',
                    )}
                />
            </Button>
        </div>
    );
}
