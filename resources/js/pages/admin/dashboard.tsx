import { Skeleton } from '@/components/ui/skeleton';
import AdminLayout from '@/layouts/admin-layout';
import { Deferred, Head } from '@inertiajs/react';
import { Bot, FolderTree, Layers } from 'lucide-react';

interface Stats {
    totalAgents: number;
    totalCategories: number;
    totalConfigTypes: number;
}

interface AdminDashboardProps {
    stats: Stats;
}

function StatsGrid({ stats }: { stats: Stats }) {
    const statCards = [
        {
            label: 'AI Agents',
            value: stats.totalAgents,
            icon: Bot,
            href: '/admin/agents',
        },
        {
            label: 'Categories',
            value: stats.totalCategories,
            icon: FolderTree,
            href: '/admin/categories',
        },
        {
            label: 'Config Types',
            value: stats.totalConfigTypes,
            icon: Layers,
            href: '/admin/config-types',
        },
    ];

    return (
        <div className="grid gap-4 md:grid-cols-3">
            {statCards.map((stat) => (
                <a
                    key={stat.label}
                    href={stat.href}
                    className="flex items-center gap-4 rounded-lg border bg-card p-6 transition-colors hover:bg-accent"
                >
                    <div className="flex size-12 items-center justify-center rounded-lg bg-primary/10">
                        <stat.icon className="size-6 text-primary" />
                    </div>
                    <div>
                        <p className="text-2xl font-bold">{stat.value}</p>
                        <p className="text-sm text-muted-foreground">
                            {stat.label}
                        </p>
                    </div>
                </a>
            ))}
        </div>
    );
}

function StatsGridSkeleton() {
    return (
        <div className="grid gap-4 md:grid-cols-3">
            {[1, 2, 3].map((i) => (
                <div
                    key={i}
                    className="flex items-center gap-4 rounded-lg border bg-card p-6"
                >
                    <Skeleton className="size-12 rounded-lg" />
                    <div className="space-y-2">
                        <Skeleton className="h-7 w-12" />
                        <Skeleton className="h-4 w-20" />
                    </div>
                </div>
            ))}
        </div>
    );
}

export default function AdminDashboard({ stats }: AdminDashboardProps) {
    return (
        <AdminLayout
            breadcrumbs={[{ title: 'Admin Dashboard', href: '/admin' }]}
        >
            <Head title="Admin Dashboard" />

            <div className="flex flex-col gap-6 p-6">
                <div>
                    <h1 className="text-2xl font-bold">Admin Dashboard</h1>
                    <p className="text-muted-foreground">
                        Manage AI agents, categories, and configuration types.
                    </p>
                </div>

                <Deferred data="stats" fallback={<StatsGridSkeleton />}>
                    <StatsGrid stats={stats} />
                </Deferred>
            </div>
        </AdminLayout>
    );
}
