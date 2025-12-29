import InputError from '@/components/input-error';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { MarkdownEditor } from '@/components/markdown-editor';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import type { SubmitMcpServerPageProps } from '@/types/models';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface KeyValuePair {
    key: string;
    value: string;
}

export default function SubmitMcpServer({}: SubmitMcpServerPageProps) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const { data, setData, errors, setError } = useForm<{
        name: string;
        description: string;
        type: 'remote' | 'local';
        url: string;
        command: string;
        args: string[];
        env: KeyValuePair[];
        headers: KeyValuePair[];
        source_url: string;
        source_author: string;
        readme: string;
    }>({
        name: '',
        description: '',
        type: 'local',
        url: '',
        command: '',
        args: [''],
        env: [{ key: '', value: '' }],
        headers: [{ key: '', value: '' }],
        source_url: '',
        source_author: '',
        readme: '',
    });

    const addArg = () => {
        setData('args', [...data.args, '']);
    };

    const removeArg = (index: number) => {
        if (data.args.length > 1) {
            setData(
                'args',
                data.args.filter((_, i) => i !== index),
            );
        }
    };

    const updateArg = (index: number, value: string) => {
        const newArgs = [...data.args];
        newArgs[index] = value;
        setData('args', newArgs);
    };

    const addEnv = () => {
        setData('env', [...data.env, { key: '', value: '' }]);
    };

    const removeEnv = (index: number) => {
        if (data.env.length > 1) {
            setData(
                'env',
                data.env.filter((_, i) => i !== index),
            );
        }
    };

    const updateEnv = (
        index: number,
        field: 'key' | 'value',
        value: string,
    ) => {
        const newEnv = [...data.env];
        newEnv[index] = { ...newEnv[index], [field]: value };
        setData('env', newEnv);
    };

    const addHeader = () => {
        setData('headers', [...data.headers, { key: '', value: '' }]);
    };

    const removeHeader = (index: number) => {
        if (data.headers.length > 1) {
            setData(
                'headers',
                data.headers.filter((_, i) => i !== index),
            );
        }
    };

    const updateHeader = (
        index: number,
        field: 'key' | 'value',
        value: string,
    ) => {
        const newHeaders = [...data.headers];
        newHeaders[index] = { ...newHeaders[index], [field]: value };
        setData('headers', newHeaders);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Transform data for submission
        const submitData = {
            name: data.name,
            description: data.description,
            type: data.type,
            url: data.type === 'remote' ? data.url : null,
            command: data.type === 'local' ? data.command : null,
            args:
                data.type === 'local'
                    ? data.args.filter((arg) => arg.trim() !== '')
                    : null,
            env: data.env.some((e) => e.key.trim() !== '')
                ? Object.fromEntries(
                      data.env
                          .filter((e) => e.key.trim() !== '')
                          .map((e) => [e.key, e.value]),
                  )
                : null,
            headers:
                data.type === 'remote' &&
                data.headers.some((h) => h.key.trim() !== '')
                    ? Object.fromEntries(
                          data.headers
                              .filter((h) => h.key.trim() !== '')
                              .map((h) => [h.key, h.value]),
                      )
                    : null,
            source_url: data.source_url || null,
            source_author: data.source_author || null,
            readme: data.readme || null,
        };

        router.post('/submit/mcp-server', submitData, {
            onError: (errors) => {
                Object.entries(errors).forEach(([key, value]) => {
                    setError(key as keyof typeof data, value as string);
                });
                setIsSubmitting(false);
            },
            onFinish: () => setIsSubmitting(false),
        });
    };

    return (
        <>
            <Head title="Submit MCP Server" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <Link
                                href="/submit"
                                className="mb-4 inline-flex items-center gap-2 text-sm text-ds-text-muted hover:text-ds-text-primary"
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Back to Submit
                            </Link>
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                Submit MCP Server
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Share a Model Context Protocol server with the
                                community
                            </p>
                        </div>
                    </section>

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[800px] px-4 py-8 md:px-6 md:py-12">
                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Basic Info */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Basic Information
                                    </h2>

                                    <div className="grid gap-2">
                                        <Label htmlFor="name">Name</Label>
                                        <Input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder="My MCP Server"
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="description">
                                            Description
                                        </Label>
                                        <textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) =>
                                                setData(
                                                    'description',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Describe what this MCP server does and its capabilities..."
                                            className="flex min-h-[100px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError
                                            message={errors.description}
                                        />
                                    </div>
                                </div>

                                {/* Server Type */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Server Configuration
                                    </h2>

                                    <div className="grid gap-2">
                                        <Label htmlFor="type">
                                            Server Type
                                        </Label>
                                        <Select
                                            value={data.type}
                                            onValueChange={(
                                                value: 'remote' | 'local',
                                            ) => setData('type', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="local">
                                                    Local (command-based)
                                                </SelectItem>
                                                <SelectItem value="remote">
                                                    Remote (URL-based)
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <p className="text-xs text-ds-text-muted">
                                            {data.type === 'local'
                                                ? "Local servers run via a command on the user's machine"
                                                : 'Remote servers connect via HTTP/HTTPS URL'}
                                        </p>
                                        <InputError message={errors.type} />
                                    </div>

                                    {data.type === 'remote' ? (
                                        <div className="grid gap-2">
                                            <Label htmlFor="url">
                                                Server URL
                                            </Label>
                                            <Input
                                                id="url"
                                                type="url"
                                                value={data.url}
                                                onChange={(e) =>
                                                    setData(
                                                        'url',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="https://api.example.com/mcp"
                                                required
                                            />
                                            <InputError message={errors.url} />
                                        </div>
                                    ) : (
                                        <>
                                            <div className="grid gap-2">
                                                <Label htmlFor="command">
                                                    Command
                                                </Label>
                                                <Input
                                                    id="command"
                                                    type="text"
                                                    value={data.command}
                                                    onChange={(e) =>
                                                        setData(
                                                            'command',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="npx -y @example/mcp-server"
                                                    required
                                                />
                                                <InputError
                                                    message={errors.command}
                                                />
                                            </div>

                                            {/* Args */}
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <Label>
                                                        Arguments (optional)
                                                    </Label>
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={addArg}
                                                        className="border-ds-border"
                                                    >
                                                        <Plus className="mr-2 h-4 w-4" />
                                                        Add Arg
                                                    </Button>
                                                </div>
                                                {data.args.map((arg, index) => (
                                                    <div
                                                        key={index}
                                                        className="flex items-center gap-2"
                                                    >
                                                        <Input
                                                            type="text"
                                                            value={arg}
                                                            onChange={(e) =>
                                                                updateArg(
                                                                    index,
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder="--flag"
                                                        />
                                                        {data.args.length >
                                                            1 && (
                                                            <Button
                                                                type="button"
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() =>
                                                                    removeArg(
                                                                        index,
                                                                    )
                                                                }
                                                                className="text-red-500 hover:text-red-400"
                                                            >
                                                                <Trash2 className="h-4 w-4" />
                                                            </Button>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    )}
                                </div>

                                {/* Environment Variables */}
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                            Environment Variables (optional)
                                        </h2>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={addEnv}
                                            className="border-ds-border"
                                        >
                                            <Plus className="mr-2 h-4 w-4" />
                                            Add Env
                                        </Button>
                                    </div>
                                    {data.env.map((env, index) => (
                                        <div
                                            key={index}
                                            className="flex items-center gap-2"
                                        >
                                            <Input
                                                type="text"
                                                value={env.key}
                                                onChange={(e) =>
                                                    updateEnv(
                                                        index,
                                                        'key',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="API_KEY"
                                                className="flex-1"
                                            />
                                            <Input
                                                type="text"
                                                value={env.value}
                                                onChange={(e) =>
                                                    updateEnv(
                                                        index,
                                                        'value',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="your-api-key"
                                                className="flex-1"
                                            />
                                            {data.env.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        removeEnv(index)
                                                    }
                                                    className="text-red-500 hover:text-red-400"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                {/* Headers (for remote only) */}
                                {data.type === 'remote' && (
                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between">
                                            <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                                HTTP Headers (optional)
                                            </h2>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                onClick={addHeader}
                                                className="border-ds-border"
                                            >
                                                <Plus className="mr-2 h-4 w-4" />
                                                Add Header
                                            </Button>
                                        </div>
                                        {data.headers.map((header, index) => (
                                            <div
                                                key={index}
                                                className="flex items-center gap-2"
                                            >
                                                <Input
                                                    type="text"
                                                    value={header.key}
                                                    onChange={(e) =>
                                                        updateHeader(
                                                            index,
                                                            'key',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Authorization"
                                                    className="flex-1"
                                                />
                                                <Input
                                                    type="text"
                                                    value={header.value}
                                                    onChange={(e) =>
                                                        updateHeader(
                                                            index,
                                                            'value',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Bearer token"
                                                    className="flex-1"
                                                />
                                                {data.headers.length > 1 && (
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            removeHeader(index)
                                                        }
                                                        className="text-red-500 hover:text-red-400"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </Button>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Source */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Source (optional)
                                    </h2>

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="source_url">
                                                Source URL
                                            </Label>
                                            <Input
                                                id="source_url"
                                                type="url"
                                                value={data.source_url}
                                                onChange={(e) =>
                                                    setData(
                                                        'source_url',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="https://github.com/..."
                                            />
                                            <InputError
                                                message={errors.source_url}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="source_author">
                                                Original Author
                                            </Label>
                                            <Input
                                                id="source_author"
                                                type="text"
                                                value={data.source_author}
                                                onChange={(e) =>
                                                    setData(
                                                        'source_author',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="@username"
                                            />
                                            <InputError
                                                message={errors.source_author}
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* README / Documentation */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Documentation (optional)
                                    </h2>
                                    <div className="grid gap-2">
                                        <Label htmlFor="readme">
                                            README / Instructions
                                        </Label>
                                        <MarkdownEditor
                                            value={data.readme}
                                            onChange={(value) =>
                                                setData('readme', value)
                                            }
                                            placeholder="Add installation instructions, usage examples, or any additional documentation..."
                                            minHeight="150px"
                                        />
                                        <p className="text-xs text-ds-text-muted">
                                            Markdown supported. Use this to
                                            provide setup instructions or usage
                                            examples.
                                        </p>
                                    </div>
                                </div>

                                {/* Submit */}
                                <div className="flex justify-end gap-4 border-t-2 border-ds-border pt-6">
                                    <Link href="/submit">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="border-ds-border"
                                        >
                                            Cancel
                                        </Button>
                                    </Link>
                                    <Button
                                        type="submit"
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting && <Spinner />}
                                        Submit MCP Server
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
