import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-2xl font-semibold text-ds-text-primary">
                        Dashboard
                    </h1>
                    <p className="mt-1 text-sm text-ds-text-muted">
                        Welcome back! Here's an overview of your activity.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden border border-ds-border bg-ds-bg-card">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-700" />
                    </div>
                    <div className="relative aspect-video overflow-hidden border border-ds-border bg-ds-bg-card">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-700" />
                    </div>
                    <div className="relative aspect-video overflow-hidden border border-ds-border bg-ds-bg-card">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-700" />
                    </div>
                </div>

                <div className="relative min-h-[400px] overflow-hidden border border-ds-border bg-ds-bg-card">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-700" />
                </div>
            </div>
        </AppLayout>
    );
}
