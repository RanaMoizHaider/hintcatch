import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';
import { Home, ShieldX } from 'lucide-react';

export default function Forbidden() {
    return (
        <>
            <Head title="Access Denied" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />
                <main className="flex flex-1 items-center justify-center px-4 py-16">
                    <div className="mx-auto max-w-md text-center">
                        <div className="mb-6 flex justify-center">
                            <ShieldX className="h-24 w-24 text-ds-text-subtle" />
                        </div>
                        <h1 className="mb-3 text-2xl font-semibold text-ds-text-primary">
                            Access Denied
                        </h1>
                        <p className="mb-8 text-ds-text-muted">
                            You don't have permission to access this resource.
                            Please sign in with an authorized account.
                        </p>
                        <div className="flex flex-col items-center justify-center gap-3 sm:flex-row">
                            <Button asChild>
                                <Link href="/">
                                    <Home className="mr-2 h-4 w-4" />
                                    Go Home
                                </Link>
                            </Button>
                            <Button asChild variant="outline">
                                <Link href="/login">Sign In</Link>
                            </Button>
                        </div>
                    </div>
                </main>
                <SiteFooter />
            </div>
        </>
    );
}
