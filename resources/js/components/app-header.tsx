'use client';

import { Breadcrumbs } from '@/components/breadcrumbs';
import { Icon } from '@/components/icon';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import Icons from '@/components/ui/icons';
import { UserMenuContent } from '@/components/user-menu-content';
import { useInitials } from '@/hooks/use-initials';
import { cn, resolveUrl } from '@/lib/utils';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import { useState } from 'react';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

interface AppHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function AppHeader({ breadcrumbs = [] }: AppHeaderProps) {
    const page = usePage<SharedData>();
    const { auth } = page.props;
    const getInitials = useInitials();
    const [mobileOpen, setMobileOpen] = useState(false);

    const isActive = (href: NavItem['href']) => {
        const hrefUrl = resolveUrl(href);
        return page.url === hrefUrl || page.url.startsWith(hrefUrl + '/');
    };

    return (
        <>
            <header className="sticky top-0 z-50 w-full border-b border-ds-border bg-ds-bg-card">
                <div className="mx-auto flex h-14 max-w-[1200px] items-center justify-between px-4 md:px-6">
                    {/* Left: Logo */}
                    <div className="flex items-center gap-6">
                        <Link
                            href={dashboard()}
                            prefetch
                            className="flex items-center gap-2"
                        >
                            <Icons.logo className="h-6 w-auto" />
                            <span className="text-lg font-medium text-ds-text-primary">
                                HintCatch
                            </span>
                        </Link>
                    </div>

                    {/* Center: Desktop Navigation */}
                    <nav className="hidden items-center gap-1 md:flex">
                        {mainNavItems.map((item) => (
                            <Link
                                key={item.title}
                                href={item.href}
                                className={cn(
                                    'flex items-center gap-2 px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                    isActive(item.href) &&
                                        'text-ds-text-primary',
                                )}
                            >
                                {item.icon && (
                                    <Icon
                                        iconNode={item.icon}
                                        className="h-4 w-4"
                                    />
                                )}
                                {item.title}
                            </Link>
                        ))}
                    </nav>

                    {/* Right: Actions */}
                    <div className="flex items-center gap-3">
                        {/* Mobile Menu Toggle */}
                        <Button
                            variant="ghost"
                            size="sm"
                            className="h-8 w-8 p-0 hover:bg-ds-bg-secondary md:hidden"
                            onClick={() => setMobileOpen(!mobileOpen)}
                            aria-expanded={mobileOpen}
                            aria-controls="mobile-nav"
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
                        </Button>

                        {/* User Menu */}
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Avatar className="size-8 cursor-pointer">
                                    <AvatarImage
                                        src={auth.user.avatar}
                                        alt={auth.user.name}
                                    />
                                    <AvatarFallback className="bg-ds-bg-secondary text-xs text-ds-text-muted">
                                        {getInitials(auth.user.name)}
                                    </AvatarFallback>
                                </Avatar>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent
                                className="min-w-56 border-ds-border bg-ds-bg-card"
                                side="bottom"
                                align="end"
                                sideOffset={8}
                            >
                                <UserMenuContent user={auth.user} />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                {/* Mobile Navigation */}
                {mobileOpen && (
                    <nav
                        id="mobile-nav"
                        className="border-t border-ds-border px-4 py-4 md:hidden"
                    >
                        <div className="flex flex-col gap-2">
                            {mainNavItems.map((item) => (
                                <Link
                                    key={item.title}
                                    href={item.href}
                                    onClick={() => setMobileOpen(false)}
                                    className={cn(
                                        'flex items-center gap-2 px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                        isActive(item.href) &&
                                            'text-ds-text-primary',
                                    )}
                                >
                                    {item.icon && (
                                        <Icon
                                            iconNode={item.icon}
                                            className="h-4 w-4"
                                        />
                                    )}
                                    {item.title}
                                </Link>
                            ))}
                        </div>
                    </nav>
                )}
            </header>

            {/* Breadcrumbs */}
            {breadcrumbs.length > 1 && (
                <div className="border-b border-ds-border bg-ds-bg-base">
                    <div className="mx-auto flex h-10 max-w-[1200px] items-center px-4 md:px-6">
                        <Breadcrumbs breadcrumbs={breadcrumbs} />
                    </div>
                </div>
            )}
        </>
    );
}
