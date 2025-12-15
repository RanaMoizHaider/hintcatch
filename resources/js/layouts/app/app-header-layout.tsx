import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import type { PropsWithChildren } from 'react';

export default function AppHeaderLayout({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col bg-ds-bg-base">
            <SiteHeader />
            <main className="mx-auto w-full max-w-[1200px] flex-1 px-4 py-8 md:px-6">
                {children}
            </main>
            <SiteFooter />
        </div>
    );
}
