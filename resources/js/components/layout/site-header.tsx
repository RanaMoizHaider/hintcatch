import { index as agentsIndex } from '@/actions/App/Http/Controllers/AgentController';
import { index as configTypesIndex } from '@/actions/App/Http/Controllers/ConfigTypeController';
import { index as mcpServersIndex } from '@/actions/App/Http/Controllers/McpServerController';
import { index as promptsIndex } from '@/actions/App/Http/Controllers/PromptController';
import { index as skillsIndex } from '@/actions/App/Http/Controllers/SkillController';
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
import { dashboard, login, logout } from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LogOut, Plus, Settings, User } from 'lucide-react';
import { useState } from 'react';

const navItems = [
    { label: 'AI Agents', href: agentsIndex },
    { label: 'Configs', href: configTypesIndex },
    { label: 'MCPs', href: mcpServersIndex },
    { label: 'Agent Skills', href: skillsIndex },
    { label: 'Prompts', href: promptsIndex },
];

export function SiteHeader() {
    const { auth } = usePage<SharedData>().props;
    const getInitials = useInitials();
    const currentPath = usePage().url;
    const [mobileOpen, setMobileOpen] = useState(false);

    const user = auth?.user;

    const isActive = (path: string) => {
        return currentPath === path || currentPath.startsWith(path + '/');
    };

    const submitHref = user ? '/submit' : login();

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

                {/* Center: Navigation - Always visible */}
                <nav className="hidden items-center gap-1 md:flex">
                    {navItems.map((item) => (
                        <Link
                            key={item.href().url}
                            href={item.href()}
                            className={cn(
                                'px-3 py-2 text-sm text-ds-text-muted uppercase transition-colors hover:text-ds-text-primary',
                                isActive(item.href().url) &&
                                    'text-ds-text-primary',
                            )}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>

                {/* Right: Actions */}
                <div className="flex items-center gap-3">
                    {/* Submit Button - Always visible */}
                    <Button
                        asChild
                        size="sm"
                        className="hidden bg-ds-text-primary text-ds-bg-base hover:bg-ds-text-secondary md:flex"
                    >
                        <Link href={submitHref}>
                            <Plus className="mr-1 h-4 w-4" />
                            Submit
                        </Link>
                    </Button>

                    {user ? (
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
                    ) : (
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
                        </>
                    )}
                </div>
            </div>

            {/* Mobile Navigation */}
            {mobileOpen && (
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
                                    'px-3 py-2 text-sm text-ds-text-muted uppercase transition-colors hover:text-ds-text-primary',
                                    isActive(item.href().url) &&
                                        'text-ds-text-primary',
                                )}
                            >
                                {item.label}
                            </Link>
                        ))}
                        <div className="my-2 border-t border-ds-border" />
                        <Link
                            href={submitHref}
                            onClick={() => setMobileOpen(false)}
                            className="px-3 py-2 text-sm font-medium text-ds-text-primary transition-colors hover:text-ds-text-secondary"
                        >
                            <Plus className="mr-1 inline h-4 w-4" />
                            Submit
                        </Link>
                        {!user && (
                            <Link
                                href={login()}
                                onClick={() => setMobileOpen(false)}
                                className="px-3 py-2 text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                            >
                                Log in
                            </Link>
                        )}
                    </div>
                </nav>
            )}
        </header>
    );
}
