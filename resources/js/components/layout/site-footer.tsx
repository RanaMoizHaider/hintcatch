'use client';

import { Icons } from '@/components/ui/icons';
import { Link, usePage } from '@inertiajs/react';

const footerLinks = [
    {
        name: 'GitHub',
        href: 'https://github.com/ranamoizhaider/hintcatch',
        external: true,
    },
    { name: 'About', href: '/about', external: false },
];

export function SiteFooter() {
    const currentPath = usePage().url;

    // Hide footer on login/register pages
    if (currentPath.includes('login') || currentPath.includes('register')) {
        return null;
    }

    return (
        <footer className="mt-auto w-full border-t border-ds-border bg-ds-bg-card">
            {/* Main Footer Links */}
            <div className="mx-auto flex max-w-[1200px] flex-wrap items-center justify-center gap-x-6 gap-y-2 px-4 py-4 md:justify-start md:px-6">
                {footerLinks.map((link) =>
                    link.external ? (
                        <a
                            key={link.name}
                            href={link.href}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                        >
                            {link.name}
                        </a>
                    ) : (
                        <Link
                            key={link.name}
                            href={link.href}
                            className="text-sm text-ds-text-muted transition-colors hover:text-ds-text-primary"
                        >
                            {link.name}
                        </Link>
                    ),
                )}
            </div>

            {/* Copyright Bar */}
            <div className="border-t border-ds-border">
                <div className="mx-auto flex max-w-[1200px] flex-wrap items-center justify-between gap-4 px-4 py-3 md:px-6">
                    <span className="text-sm text-ds-text-subtle">
                        © {new Date().getFullYear()} HintCatch
                    </span>
                    <div className="flex items-center gap-4">
                        <a
                            href="https://github.com/ranamoizhaider/hintcatch"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-ds-text-muted transition-colors hover:text-ds-text-primary"
                            aria-label="GitHub"
                        >
                            <Icons.github className="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    );
}
