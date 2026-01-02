import { SiteFooter } from '@/components/layout/site-footer';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Icons } from '@/components/ui/icons';
import { useInitials } from '@/hooks/use-initials';
import { cn } from '@/lib/utils';
import { dashboard, logout } from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    Bot,
    FolderTree,
    LayoutDashboard,
    LogOut,
    Settings,
    Shapes,
    User,
} from 'lucide-react';
import { useState, type PropsWithChildren } from 'react';

interface AdminLayoutProps extends PropsWithChildren {
    breadcrumbs?: { title: string; href: string }[];
}

const adminNavItems = [
    { label: 'Dashboard', href: '/admin', icon: LayoutDashboard },
    { label: 'Agents', href: '/admin/agents', icon: Bot },
    { label: 'Categories', href: '/admin/categories', icon: FolderTree },
    { label: 'Config Types', href: '/admin/config-types', icon: Shapes },
];

export default function AdminLayout({ children }: AdminLayoutProps) {
    const { auth } = usePage<SharedData>().props;
    const getInitials = useInitials();
    const currentPath = usePage().url;
    const [mobileOpen, setMobileOpen] = useState(false);

    const user = auth?.user;

    const isActive = (path: string) => {
        if (path === '/admin') {
            return currentPath === '/admin';
        }
        return currentPath.startsWith(path);
    };

    return (
        <div className="flex min-h-screen flex-col bg-ds-bg-base">
            <header className="w-full border-b border-ds-border bg-ds-bg-card">
                <div className="mx-auto flex h-14 max-w-[1200px] items-center justify-between px-4 md:px-6">
                    <div className="flex items-center gap-6">
                        <Link href="/" className="flex items-center gap-2">
                            <Icons.logo className="h-6 w-auto" />
                            <span className="text-lg font-medium text-ds-text-primary">
                                HintCatch
                            </span>
                        </Link>
                        <span className="bg-ds-accent-muted text-ds-accent rounded px-2 py-0.5 text-xs font-medium">
                            Admin
                        </span>
                    </div>

                    <nav className="hidden items-center gap-1 md:flex">
                        {adminNavItems.map((item) => (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    'flex items-center gap-1.5 px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                    isActive(item.href) &&
                                        'font-medium text-ds-text-primary',
                                )}
                            >
                                <item.icon className="size-4" />
                                {item.label}
                            </Link>
                        ))}
                    </nav>

                    <div className="flex items-center gap-3">
                        <Button
                            asChild
                            variant="ghost"
                            size="sm"
                            className="text-ds-text-muted hover:text-ds-text-primary"
                        >
                            <Link href="/">← Back to Site</Link>
                        </Button>

                        {user && (
                            <>
                                <button
                                    type="button"
                                    className="flex h-8 w-8 items-center justify-center text-ds-text-muted hover:text-ds-text-primary md:hidden"
                                    onClick={() => setMobileOpen(!mobileOpen)}
                                    aria-expanded={mobileOpen}
                                    aria-controls="admin-mobile-nav"
                                >
                                    <span className="sr-only">Toggle menu</span>
                                    <div className="relative flex h-4 w-4 flex-col items-center justify-center">
                                        <span
                                            className={cn(
                                                'absolute h-px w-4 bg-current transition-all duration-200',
                                                mobileOpen
                                                    ? 'rotate-45'
                                                    : '-translate-y-1.5',
                                            )}
                                        />
                                        <span
                                            className={cn(
                                                'absolute h-px w-4 bg-current transition-all duration-200',
                                                mobileOpen && 'opacity-0',
                                            )}
                                        />
                                        <span
                                            className={cn(
                                                'absolute h-px w-4 bg-current transition-all duration-200',
                                                mobileOpen
                                                    ? '-rotate-45'
                                                    : 'translate-y-1.5',
                                            )}
                                        />
                                    </div>
                                </button>

                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Avatar className="size-8 cursor-pointer">
                                            <AvatarImage
                                                src={user.avatar}
                                                alt={user.name}
                                            />
                                            <AvatarFallback className="bg-ds-bg-secondary text-xs text-ds-text-muted">
                                                {getInitials(user.name)}
                                            </AvatarFallback>
                                        </Avatar>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        className="min-w-56 border-ds-border bg-ds-bg-card"
                                        side="bottom"
                                        align="end"
                                        sideOffset={8}
                                    >
                                        <DropdownMenuLabel className="p-0 font-normal">
                                            <div className="flex items-center gap-2 px-2 py-2 text-left text-sm">
                                                <Avatar className="h-8 w-8">
                                                    <AvatarImage
                                                        src={user.avatar}
                                                        alt={user.name}
                                                    />
                                                    <AvatarFallback className="bg-ds-bg-secondary text-ds-text-muted">
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
                                        </DropdownMenuLabel>
                                        <DropdownMenuSeparator className="bg-ds-border" />
                                        <DropdownMenuItem
                                            asChild
                                            className="text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                        >
                                            <Link href={dashboard()}>
                                                <User className="mr-2 h-4 w-4" />
                                                Dashboard
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            asChild
                                            className="text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                        >
                                            <Link href={editAppearance()}>
                                                <Settings className="mr-2 h-4 w-4" />
                                                Settings
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuSeparator className="bg-ds-border" />
                                        <DropdownMenuItem
                                            asChild
                                            className="text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                        >
                                            <Link
                                                href={logout()}
                                                method="post"
                                                as="button"
                                                className="w-full"
                                            >
                                                <LogOut className="mr-2 h-4 w-4" />
                                                Log out
                                            </Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </>
                        )}
                    </div>
                </div>

                {mobileOpen && (
                    <nav
                        id="admin-mobile-nav"
                        className="border-t border-ds-border px-4 py-4 md:hidden"
                    >
                        <div className="flex flex-col gap-2">
                            {adminNavItems.map((item) => (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    onClick={() => setMobileOpen(false)}
                                    className={cn(
                                        'flex items-center gap-2 px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                        isActive(item.href) &&
                                            'font-medium text-ds-text-primary',
                                    )}
                                >
                                    <item.icon className="size-4" />
                                    {item.label}
                                </Link>
                            ))}
                        </div>
                    </nav>
                )}
            </header>

            <main className="mx-auto w-full max-w-[1200px] flex-1 px-4 py-8 md:px-6">
                {children}
            </main>

            <SiteFooter />
        </div>
    );
}
