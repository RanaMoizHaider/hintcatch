import { toggle } from '@/actions/App/Http/Controllers/FavoriteController';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { Heart } from 'lucide-react';
import { useState, useTransition } from 'react';

interface FavoriteButtonProps {
    favoritableType: 'config' | 'prompt' | 'mcp-server' | 'skill';
    favoritableId: number;
    isFavorited: boolean;
    favoritesCount?: number;
    className?: string;
}

export function FavoriteButton({
    favoritableType,
    favoritableId,
    isFavorited: initialIsFavorited,
    favoritesCount: initialCount,
    className,
}: FavoriteButtonProps) {
    const { auth } = usePage<SharedData>().props;
    const [isFavorited, setIsFavorited] = useState(initialIsFavorited);
    const [count, setCount] = useState(initialCount ?? 0);
    const [isPending, startTransition] = useTransition();

    const handleToggle = () => {
        if (!auth.user) {
            router.get('/login');
            return;
        }

        const previousIsFavorited = isFavorited;
        const previousCount = count;

        const newIsFavorited = !isFavorited;
        const newCount = newIsFavorited ? count + 1 : count - 1;

        setIsFavorited(newIsFavorited);
        setCount(newCount);

        startTransition(() => {
            router.post(
                toggle.url(),
                {
                    favoritable_type: favoritableType,
                    favoritable_id: favoritableId,
                },
                {
                    preserveScroll: true,
                    onError: () => {
                        setIsFavorited(previousIsFavorited);
                        setCount(previousCount);
                    },
                },
            );
        });
    };

    return (
        <Button
            variant="ghost"
            size="sm"
            className={cn(
                'group h-8 gap-1.5 px-2 hover:bg-transparent',
                isFavorited
                    ? 'text-red-500 hover:text-red-600'
                    : 'text-ds-text-muted hover:text-ds-text-primary',
                className,
            )}
            onClick={handleToggle}
            disabled={isPending}
            aria-label={isFavorited ? 'Unfavorite' : 'Favorite'}
        >
            <Heart
                className={cn(
                    'h-4 w-4 transition-all',
                    isFavorited && 'fill-current',
                )}
            />
            <span className="text-xs font-medium tabular-nums">{count}</span>
        </Button>
    );
}
