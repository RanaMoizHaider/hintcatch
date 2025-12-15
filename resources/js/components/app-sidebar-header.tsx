import { Breadcrumbs } from '@/components/breadcrumbs';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    return (
        <header className="flex h-14 shrink-0 items-center gap-3 border-b border-ds-border bg-ds-bg-card px-4">
            <SidebarTrigger className="-ml-1 text-ds-text-muted hover:bg-ds-bg-secondary hover:text-ds-text-primary" />
            {breadcrumbs.length > 0 && (
                <>
                    <Separator
                        orientation="vertical"
                        className="h-4 bg-ds-border"
                    />
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                </>
            )}
        </header>
    );
}
