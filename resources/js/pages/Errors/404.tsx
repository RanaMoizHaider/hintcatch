import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';
import { Home, Search } from 'lucide-react';

export default function NotFound() {
    return (
        <>
            <Head title="Page Not Found" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />
                <main className="flex flex-1 items-center justify-center px-4 py-16">
                    <div className="mx-auto max-w-md text-center">
                        <div className="mb-6 text-8xl font-bold text-ds-text-subtle">
                            404
                        </div>
                        <h1 className="mb-3 text-2xl font-semibold text-ds-text-primary">
                            Page Not Found
                        </h1>
                        <p className="mb-8 text-ds-text-muted">
                            The page you're looking for doesn't exist or has
                            been moved.
                        </p>
                        <div className="flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <Button asChild>
                                <Link href="/">
                                    <Home className="mr-2 h-4 w-4" />
                                    Go Home
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href="/agents">
                                    <Search className="mr-2 h-4 w-4" />
                                    Browse Agents
                                </Link>
                            </Button>
                        </div>
                    </div>
                </main>
                <SiteFooter />
            </div>
        </>
    );
}
