import { type Column, DataTable } from '@/components/admin/data-table';
import {
    AvatarCell,
    BadgeList,
    BooleanBadge,
    LinksCell,
} from '@/components/admin/table-cells';
import AdminLayout from '@/layouts/admin-layout';
import { type Agent } from '@/types/models';
import { Head, router } from '@inertiajs/react';

interface AgentsIndexProps {
    agents: Agent[];
}

export default function AgentsIndex({ agents }: AgentsIndexProps) {
    const columns: Column<Agent>[] = [
        {
            key: 'agent',
            header: 'Agent',
            render: (agent) => (
                <AvatarCell
                    image={agent.logo}
                    name={agent.name}
                    subtitle={agent.slug}
                    extra={agent.rules_filename ?? undefined}
                />
            ),
        },
        {
            key: 'mcp',
            header: 'MCP Support',
            headerClassName: 'hidden md:table-cell',
            className: 'hidden md:table-cell',
            render: (agent) => (
                <div className="flex items-center gap-2">
                    <BooleanBadge
                        value={agent.supports_mcp}
                        trueLabel="MCP"
                        falseLabel="No MCP"
                    />
                    {agent.supports_mcp &&
                        agent.mcp_transport_types &&
                        agent.mcp_transport_types.length > 0 && (
                            <span className="text-xs text-ds-text-subtle">
                                {agent.mcp_transport_types.join(', ')}
                            </span>
                        )}
                </div>
            ),
        },
        {
            key: 'config_types',
            header: 'Config Types',
            headerClassName: 'hidden lg:table-cell',
            className: 'hidden lg:table-cell',
            render: (agent) => (
                <BadgeList
                    items={agent.supported_config_types}
                    maxVisible={3}
                />
            ),
        },
        {
            key: 'links',
            header: 'Links',
            headerClassName: 'hidden sm:table-cell',
            className: 'hidden sm:table-cell',
            render: (agent) => (
                <LinksCell
                    website={agent.website}
                    github={agent.github_url}
                    docs={agent.docs_url}
                />
            ),
        },
    ];

    return (
        <AdminLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title: 'Agents', href: '/admin/agents' },
            ]}
        >
            <Head title="Manage Agents" />

            <DataTable
                title="AI Agents"
                description="Manage AI coding agents and their configurations."
                data={agents}
                columns={columns}
                createHref="/admin/agents/create"
                editHref={(agent) => `/admin/agents/${agent.id}/edit`}
                onDelete={(agent) => router.delete(`/admin/agents/${agent.id}`)}
                deleteTitle="Delete Agent"
                deleteDescription={(agent) =>
                    `Are you sure you want to delete "${agent.name}"? This will also remove all associated configurations.`
                }
                searchPlaceholder="Search agents..."
                searchFilter={(agent, search) =>
                    agent.name.toLowerCase().includes(search) ||
                    agent.slug.toLowerCase().includes(search)
                }
                emptyMessage="No agents found."
            />
        </AdminLayout>
    );
}
