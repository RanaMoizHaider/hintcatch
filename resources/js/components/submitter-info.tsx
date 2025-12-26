import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/hooks/use-initials';
import { Link } from '@inertiajs/react';

interface SubmitterInfoProps {
    user?: {
        id: number;
        name: string;
        username: string;
        avatar?: string | null;
    };
    sourceAuthor?: string | null;
}

export function SubmitterInfo({ user, sourceAuthor }: SubmitterInfoProps) {
    const getInitials = useInitials();

    if (!user && !sourceAuthor) {
        return null;
    }

    return (
        <div className="flex items-center gap-3">
            {user ? (
                <>
                    <Link href={`/users/${user.username}`}>
                        <Avatar className="h-8 w-8 border-2 border-ds-border">
                            <AvatarImage
                                src={user.avatar ?? undefined}
                                alt={user.name}
                            />
                            <AvatarFallback className="bg-ds-bg-card text-xs text-ds-text-muted">
                                {getInitials(user.name)}
                            </AvatarFallback>
                        </Avatar>
                    </Link>
                    <div className="flex flex-col justify-center">
                        <Link
                            href={`/users/${user.username}`}
                            className="text-sm leading-tight text-ds-text-primary hover:text-ds-text-secondary"
                        >
                            {user.name}
                        </Link>
                        <div className="text-xs leading-tight text-ds-text-muted">
                            @{user.username}
                        </div>
                    </div>
                </>
            ) : (
                <span className="text-sm text-ds-text-secondary">
                    {sourceAuthor}
                </span>
            )}
        </div>
    );
}
