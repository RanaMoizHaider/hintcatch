import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Link } from '@inertiajs/react';
import { Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { type ReactNode, useState } from 'react';

export interface Column<T> {
    key: string;
    header: string;
    className?: string;
    headerClassName?: string;
    render: (item: T) => ReactNode;
}

interface DataTableProps<T extends { id: number }> {
    title: string;
    description: string;
    data: T[];
    columns: Column<T>[];
    createHref: string;
    editHref: (item: T) => string;
    onDelete: (item: T) => void;
    deleteTitle?: string;
    deleteDescription?: (item: T) => string;
    searchPlaceholder?: string;
    searchFilter?: (item: T, search: string) => boolean;
    emptyMessage?: string;
}

export function DataTable<T extends { id: number; name?: string }>({
    title,
    description,
    data,
    columns,
    createHref,
    editHref,
    onDelete,
    deleteTitle = 'Delete Item',
    deleteDescription,
    searchPlaceholder = 'Search...',
    searchFilter,
    emptyMessage = 'No items found.',
}: DataTableProps<T>) {
    const [search, setSearch] = useState('');

    const filteredData = searchFilter
        ? data.filter((item) => searchFilter(item, search.toLowerCase()))
        : data;

    return (
        <div className="flex flex-col gap-6">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-2xl font-bold text-ds-text-primary">
                        {title}
                    </h1>
                    <p className="text-sm text-ds-text-muted">{description}</p>
                </div>
                <Button asChild>
                    <Link href={createHref}>
                        <Plus className="mr-2 size-4" />
                        Add New
                    </Link>
                </Button>
            </div>

            <div className="relative max-w-sm">
                <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ds-text-muted" />
                <Input
                    placeholder={searchPlaceholder}
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-9"
                />
            </div>

            <div className="overflow-hidden border border-ds-border bg-ds-bg-card">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-ds-border bg-ds-bg-secondary">
                            {columns.map((col) => (
                                <th
                                    key={col.key}
                                    className={`px-4 py-3 text-left text-xs font-medium tracking-wider text-ds-text-muted uppercase ${col.headerClassName ?? ''}`}
                                >
                                    {col.header}
                                </th>
                            ))}
                            <th className="px-4 py-3 text-right text-xs font-medium tracking-wider text-ds-text-muted uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-ds-border">
                        {filteredData.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length + 1}
                                    className="px-4 py-12 text-center text-ds-text-muted"
                                >
                                    {search
                                        ? 'No items match your search.'
                                        : emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            filteredData.map((item) => (
                                <tr
                                    key={item.id}
                                    className="transition-colors hover:bg-ds-bg-secondary/50"
                                >
                                    {columns.map((col) => (
                                        <td
                                            key={col.key}
                                            className={`px-4 py-4 ${col.className ?? ''}`}
                                        >
                                            {col.render(item)}
                                        </td>
                                    ))}
                                    <td className="px-4 py-4">
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                asChild
                                                className="size-8"
                                            >
                                                <Link href={editHref(item)}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <AlertDialog>
                                                <AlertDialogTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        className="size-8"
                                                    >
                                                        <Trash2 className="size-4 text-destructive" />
                                                    </Button>
                                                </AlertDialogTrigger>
                                                <AlertDialogContent className="border-ds-border bg-ds-bg-card">
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle className="text-ds-text-primary">
                                                            {deleteTitle}
                                                        </AlertDialogTitle>
                                                        <AlertDialogDescription className="text-ds-text-muted">
                                                            {deleteDescription
                                                                ? deleteDescription(
                                                                      item,
                                                                  )
                                                                : `Are you sure you want to delete "${item.name}"?`}
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel className="border-ds-border">
                                                            Cancel
                                                        </AlertDialogCancel>
                                                        <AlertDialogAction
                                                            onClick={() =>
                                                                onDelete(item)
                                                            }
                                                            className="bg-destructive text-white hover:bg-destructive/90"
                                                        >
                                                            Delete
                                                        </AlertDialogAction>
                                                    </AlertDialogFooter>
                                                </AlertDialogContent>
                                            </AlertDialog>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            <div className="text-sm text-ds-text-muted">
                {filteredData.length} of {data.length} items
            </div>
        </div>
    );
}
