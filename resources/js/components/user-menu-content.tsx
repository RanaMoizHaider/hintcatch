import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { useInitials } from '@/hooks/use-initials';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import { type User } from '@/types';
import { Link, router } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';

interface UserMenuContentProps {
    user: User;
}

export function UserMenuContent({ user }: UserMenuContentProps) {
    const cleanup = useMobileNavigation();
    const getInitials = useInitials();

    const handleLogout = () => {
        cleanup();
        router.flushAll();
    };

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <Link href={edit()} className="rounded-none" prefetch>
                    <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm hover:bg-ds-bg-secondary">
                        <Avatar className="h-8 w-8 rounded-none">
                            <AvatarImage src={user.avatar} alt={user.name} />
                            <AvatarFallback className="rounded-none bg-ds-bg-secondary text-ds-text-muted">
                                {getInitials(user.name)}
                            </AvatarFallback>
                        </Avatar>
                        <div className="grid flex-1 text-left text-sm leading-tight">
                            <span className="truncate font-medium text-ds-text-primary">
                                {user.name}
                            </span>
                            <span className="truncate text-xs text-ds-text-muted">
                                {user.email}
                            </span>
                        </div>
                    </div>
                </Link>
            </DropdownMenuLabel>
            <DropdownMenuSeparator className="bg-ds-bg-secondary" />
            <DropdownMenuGroup>
                <DropdownMenuItem
                    asChild
                    className="rounded-none text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                >
                    <Link
                        className="block w-full"
                        href={edit()}
                        as="button"
                        prefetch
                        onClick={cleanup}
                    >
                        <Settings className="mr-2" />
                        Settings
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator className="bg-ds-bg-secondary" />
            <DropdownMenuItem
                asChild
                className="rounded-none text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
            >
                <Link
                    className="block w-full"
                    href={logout()}
                    as="button"
                    onClick={handleLogout}
                    data-test="logout-button"
                >
                    <LogOut className="mr-2" />
                    Log out
                </Link>
            </DropdownMenuItem>
        </>
    );
}
