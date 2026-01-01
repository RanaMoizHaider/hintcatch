import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/react';
import { RefreshCw, Wrench } from 'lucide-react';

export default function ServiceUnavailable() {
    const handleRefresh = () => {
        window.location.reload();
    };

    return (
        <>
            <Head title="Service Unavailable" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />
                <main className="flex flex-1 items-center justify-center px-4 py-16">
                    <div className="mx-auto max-w-md text-center">
                        <div className="mb-6 flex justify-center">
                            <Wrench className="h-24 w-24 text-ds-text-subtle" />
                        </div>
                        <h1 className="mb-3 text-2xl font-semibold text-ds-text-primary">
                            Under Maintenance
                        </h1>
                        <p className="mb-8 text-ds-text-muted">
                            We're currently performing scheduled maintenance.
                            Please check back in a few minutes.
                        </p>
                        <Button onClick={handleRefresh}>
                            <RefreshCw className="mr-2 h-4 w-4" />
                            Refresh Page
                        </Button>
                    </div>
                </main>
                <SiteFooter />
            </div>
        </>
    );
}
