'use client';

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
import { dashboard, login, logout, register } from '@/routes';
import { edit as editProfile } from '@/routes/profile';
import { edit as editPassword } from '@/routes/user-password';
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LogOut, Settings, User } from 'lucide-react';
import { useState } from 'react';

const navItems = [{ label: 'Dashboard', href: dashboard }];

export function SiteHeader() {
    const { auth } = usePage<SharedData>().props;
    const getInitials = useInitials();
    const currentPath = usePage().url;
    const [mobileOpen, setMobileOpen] = useState(false);

    const isActive = (path: string) => {
        return currentPath === path || currentPath.startsWith(path + '/');
    };

    return (
        <header className="w-full border-b border-ds-border bg-ds-bg-card">
            <div className="mx-auto flex h-14 max-w-[1200px] items-center justify-between px-4 md:px-6">
                {/* Left: Logo */}
                <div className="flex items-center gap-6">
                    <Link href="/" className="flex items-center gap-2">
                        <Icons.logo className="h-6 w-auto" />
                        <span className="text-lg font-medium text-ds-text-primary">
                            HintCatch
                        </span>
                    </Link>
                </div>

                {/* Center: Desktop Navigation */}
                {auth.user && (
                    <nav className="hidden items-center gap-1 md:flex">
                        {navItems.map((item) => (
                            <Link
                                key={item.href().url}
                                href={item.href()}
                                className={cn(
                                    'px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                    isActive(item.href().url) &&
                                        'text-ds-text-primary',
                                )}
                            >
                                {item.label}
                            </Link>
                        ))}
                    </nav>
                )}

                {/* Right: Actions */}
                <div className="flex items-center gap-3">
                    {auth.user ? (
                        <>
                            {/* Mobile Menu Toggle */}
                            <button
                                type="button"
                                className="flex h-8 w-8 items-center justify-center text-ds-text-muted hover:text-ds-text-primary md:hidden"
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
                            </button>

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
                                    <DropdownMenuLabel className="p-0 font-normal">
                                        <div className="flex items-center gap-2 px-2 py-2 text-left text-sm">
                                            <Avatar className="h-8 w-8">
                                                <AvatarImage
                                                    src={auth.user.avatar}
                                                    alt={auth.user.name}
                                                />
                                                <AvatarFallback className="bg-ds-bg-secondary text-ds-text-muted">
                                                    {getInitials(
                                                        auth.user.name,
                                                    )}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div className="grid flex-1 text-left text-sm leading-tight">
                                                <span className="truncate font-medium text-ds-text-primary">
                                                    {auth.user.name}
                                                </span>
                                                <span className="truncate text-xs text-ds-text-muted">
                                                    {auth.user.email}
                                                </span>
                                            </div>
                                        </div>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator className="bg-ds-border" />
                                    <DropdownMenuItem
                                        asChild
                                        className="text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                    >
                                        <Link href={editProfile()}>
                                            <User className="mr-2 h-4 w-4" />
                                            Profile
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        asChild
                                        className="text-ds-text-secondary hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                                    >
                                        <Link href={editPassword()}>
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
                    ) : (
                        <>
                            <Link
                                href={login()}
                                className="text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                            >
                                Log in
                            </Link>
                            <Button
                                asChild
                                size="sm"
                                className="bg-ds-text-primary text-ds-bg-base hover:bg-ds-text-secondary"
                            >
                                <Link href={register()}>Sign up</Link>
                            </Button>
                        </>
                    )}
                </div>
            </div>

            {/* Mobile Navigation */}
            {auth.user && mobileOpen && (
                <nav
                    id="mobile-nav"
                    className="border-t border-ds-border px-4 py-4 md:hidden"
                >
                    <div className="flex flex-col gap-2">
                        {navItems.map((item) => (
                            <Link
                                key={item.href().url}
                                href={item.href()}
                                onClick={() => setMobileOpen(false)}
                                className={cn(
                                    'px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary',
                                    isActive(item.href().url) &&
                                        'text-ds-text-primary',
                                )}
                            >
                                {item.label}
                            </Link>
                        ))}
                    </div>
                </nav>
            )}
        </header>
    );
}
