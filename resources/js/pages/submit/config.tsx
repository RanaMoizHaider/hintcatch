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
import type { Category, SubmitConfigPageProps } from '@/types/models';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';

interface ConfigFile {
    filename: string;
    content: string;
    language: string;
    path: string;
}

interface AgentInstall {
    agent_id: string;
    install_method: 'file_path' | 'cli_command' | 'custom';
    config_path: string;
    install_command?: string;
    instructions: string;
}

export default function SubmitConfig({
    agents,
    configTypes,
}: SubmitConfigPageProps) {
    const [selectedConfigTypeId, setSelectedConfigTypeId] = useState<
        string | null
    >(null);

    const { data, setData, post, processing, errors, transform } = useForm<{
        name: string;
        description: string;
        agent_id: string;
        config_type_id: string;
        category_id: string;
        source_url: string;
        github_url: string;
        source_author: string;
        readme: string;
        files: ConfigFile[];
        agent_installs: AgentInstall[];
        uses_standard_install: boolean;
        instructions: string;
    }>({
        name: '',
        description: '',
        agent_id: '',
        config_type_id: '',
        category_id: '',
        source_url: '',
        github_url: '',
        source_author: '',
        readme: '',
        files: [{ filename: '', content: '', language: 'json', path: '' }],
        agent_installs: [],
        uses_standard_install: true,
        instructions: '',
    });

    useEffect(() => {
        transform((data) => ({
            ...data,
            agent_installs: data.agent_installs.map((install) => ({
                ...install,
                install_command:
                    install.install_method === 'cli_command'
                        ? install.config_path
                        : undefined,
                config_path:
                    install.install_method !== 'cli_command'
                        ? install.config_path
                        : '',
            })),
        }));
    }, []);

    useEffect(() => {
        transform((data) => ({
            ...data,
            agent_installs: data.agent_installs.map((install) => ({
                ...install,
                // If method is CLI, map config_path (input) to install_command
                install_command:
                    install.install_method === 'cli_command'
                        ? install.config_path
                        : undefined,
                // If method is NOT CLI, keep config_path, otherwise clear it (it's in command now)
                config_path:
                    install.install_method !== 'cli_command'
                        ? install.config_path
                        : '',
            })),
        }));
    }, []);

    const selectedConfigType = configTypes.find(
        (ct) => ct.id.toString() === selectedConfigTypeId,
    );
    const categories: Category[] = selectedConfigType?.categories ?? [];
    const isPluginType = selectedConfigType?.slug === 'plugins';
    const selectedAgent = agents.find((a) => a.id.toString() === data.agent_id);

    const handleConfigTypeChange = (value: string) => {
        setSelectedConfigTypeId(value);
        setData('config_type_id', value);
        setData('category_id', ''); // Reset category when type changes
    };

    const addFile = () => {
        setData('files', [
            ...data.files,
            { filename: '', content: '', language: 'json', path: '' },
        ]);
    };

    const removeFile = (index: number) => {
        setData(
            'files',
            data.files.filter((_, i) => i !== index),
        );
    };

    const updateFile = (
        index: number,
        field: keyof ConfigFile,
        value: string,
    ) => {
        const newFiles = [...data.files];
        newFiles[index] = { ...newFiles[index], [field]: value };
        setData('files', newFiles);
    };

    const addInstall = () => {
        setData('agent_installs', [
            ...data.agent_installs,
            {
                agent_id: '',
                install_method: 'file_path',
                config_path: '',
                instructions: '',
            },
        ]);
    };

    const removeInstall = (index: number) => {
        setData(
            'agent_installs',
            data.agent_installs.filter((_, i) => i !== index),
        );
    };

    const updateInstall = (
        index: number,
        field: keyof AgentInstall,
        value: string,
    ) => {
        const newInstalls = [...data.agent_installs];
        // @ts-ignore - TS doesn't like the dynamic assignment here but it's safe
        newInstalls[index] = { ...newInstalls[index], [field]: value };
        setData('agent_installs', newInstalls);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/submit/config');
    };

    const languageOptions = [
        { value: 'json', label: 'JSON' },
        { value: 'yaml', label: 'YAML' },
        { value: 'toml', label: 'TOML' },
        { value: 'markdown', label: 'Markdown' },
        { value: 'text', label: 'Plain Text' },
        { value: 'javascript', label: 'JavaScript' },
        { value: 'typescript', label: 'TypeScript' },
    ];

    return (
        <>
            <Head title="Submit Config" />
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
                                Submit Config
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Share your configuration files with the
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
                                            placeholder="My Awesome Config"
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
                                            placeholder="Describe what this config does and how to use it..."
                                            className="flex min-h-[100px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError
                                            message={errors.description}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        README (optional)
                                    </h2>
                                    <p className="text-xs text-ds-text-secondary">
                                        Add documentation, installation
                                        instructions, or usage examples.
                                        Supports Markdown.
                                    </p>
                                    <MarkdownEditor
                                        id="readme"
                                        value={data.readme}
                                        onChange={(value) =>
                                            setData('readme', value)
                                        }
                                        placeholder="# Installation&#10;&#10;Describe how to install and use this config..."
                                        minHeight="200px"
                                    />
                                    <InputError message={errors.readme} />
                                </div>

                                {isPluginType && data.agent_id && (
                                    <div className="space-y-4">
                                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                            Installation Method
                                        </h2>

                                        <div className="space-y-4 border-2 border-ds-border bg-ds-bg-card p-4">
                                            <div className="flex items-center gap-4">
                                                <label className="flex cursor-pointer items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="install_type"
                                                        checked={
                                                            data.uses_standard_install
                                                        }
                                                        onChange={() =>
                                                            setData(
                                                                'uses_standard_install',
                                                                true,
                                                            )
                                                        }
                                                        className="text-ds-accent-primary h-4 w-4 border-ds-border bg-ds-bg-secondary"
                                                    />
                                                    <span className="text-sm text-ds-text-primary">
                                                        Uses standard{' '}
                                                        {selectedAgent?.name}{' '}
                                                        plugin installation
                                                    </span>
                                                </label>
                                            </div>

                                            <div className="flex items-center gap-4">
                                                <label className="flex cursor-pointer items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="install_type"
                                                        checked={
                                                            !data.uses_standard_install
                                                        }
                                                        onChange={() =>
                                                            setData(
                                                                'uses_standard_install',
                                                                false,
                                                            )
                                                        }
                                                        className="text-ds-accent-primary h-4 w-4 border-ds-border bg-ds-bg-secondary"
                                                    />
                                                    <span className="text-sm text-ds-text-primary">
                                                        Requires custom
                                                        installation
                                                        instructions
                                                    </span>
                                                </label>
                                            </div>

                                            {!data.uses_standard_install && (
                                                <div className="grid gap-2 pt-2">
                                                    <Label htmlFor="instructions">
                                                        Installation
                                                        Instructions (Markdown)
                                                    </Label>
                                                    <textarea
                                                        id="instructions"
                                                        value={
                                                            data.instructions
                                                        }
                                                        onChange={(e) =>
                                                            setData(
                                                                'instructions',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="Provide step-by-step installation instructions..."
                                                        className="flex min-h-[150px] w-full border border-ds-border bg-ds-bg-secondary px-3 py-2 font-mono text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                                    />
                                                    <p className="text-xs text-ds-text-muted">
                                                        Supports Markdown
                                                        formatting
                                                    </p>
                                                    <InputError
                                                        message={
                                                            errors.instructions
                                                        }
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}

                                {!data.agent_id && (
                                    <div className="space-y-4">
                                        <div className="flex items-center justify-between">
                                            <div className="space-y-1">
                                                <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                                    Agent Installation Details
                                                </h2>
                                                <p className="text-xs text-ds-text-secondary">
                                                    Specify how to install this
                                                    config for different agents.
                                                </p>
                                            </div>
                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                onClick={addInstall}
                                                className="border-ds-border"
                                            >
                                                <Plus className="mr-2 h-4 w-4" />
                                                Add Agent
                                            </Button>
                                        </div>

                                        {data.agent_installs.length === 0 && (
                                            <div className="border border-dashed border-ds-border p-8 text-center">
                                                <p className="text-sm text-ds-text-secondary">
                                                    No agent installation
                                                    details added yet.
                                                </p>
                                                <Button
                                                    type="button"
                                                    variant="link"
                                                    onClick={addInstall}
                                                    className="mt-2 text-ds-text-primary underline"
                                                >
                                                    Add one now
                                                </Button>
                                            </div>
                                        )}

                                        {data.agent_installs.map(
                                            (install, index) => (
                                                <div
                                                    key={index}
                                                    className="space-y-4 border-2 border-ds-border bg-ds-bg-card p-4"
                                                >
                                                    <div className="flex items-center justify-between">
                                                        <span className="text-sm font-medium text-ds-text-muted">
                                                            Installation Details{' '}
                                                            {index + 1}
                                                        </span>
                                                        <Button
                                                            type="button"
                                                            variant="ghost"
                                                            size="sm"
                                                            onClick={() =>
                                                                removeInstall(
                                                                    index,
                                                                )
                                                            }
                                                            className="text-red-500 hover:text-red-400"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>

                                                    <div className="grid gap-4 md:grid-cols-2">
                                                        <div className="grid gap-2">
                                                            <Label
                                                                htmlFor={`install-agent-${index}`}
                                                            >
                                                                Agent
                                                            </Label>
                                                            <Select
                                                                value={
                                                                    install.agent_id
                                                                }
                                                                onValueChange={(
                                                                    value,
                                                                ) =>
                                                                    updateInstall(
                                                                        index,
                                                                        'agent_id',
                                                                        value,
                                                                    )
                                                                }
                                                            >
                                                                <SelectTrigger>
                                                                    <SelectValue placeholder="Select an agent" />
                                                                </SelectTrigger>
                                                                <SelectContent>
                                                                    {agents.map(
                                                                        (
                                                                            agent,
                                                                        ) => (
                                                                            <SelectItem
                                                                                key={
                                                                                    agent.id
                                                                                }
                                                                                value={agent.id.toString()}
                                                                            >
                                                                                {
                                                                                    agent.name
                                                                                }
                                                                            </SelectItem>
                                                                        ),
                                                                    )}
                                                                </SelectContent>
                                                            </Select>
                                                        </div>

                                                        <div className="grid gap-2">
                                                            <Label
                                                                htmlFor={`install-method-${index}`}
                                                            >
                                                                Method
                                                            </Label>
                                                            <Select
                                                                value={
                                                                    install.install_method
                                                                }
                                                                onValueChange={(
                                                                    value,
                                                                ) =>
                                                                    updateInstall(
                                                                        index,
                                                                        'install_method',
                                                                        value as any,
                                                                    )
                                                                }
                                                            >
                                                                <SelectTrigger>
                                                                    <SelectValue />
                                                                </SelectTrigger>
                                                                <SelectContent>
                                                                    <SelectItem value="file_path">
                                                                        File
                                                                        Path
                                                                    </SelectItem>
                                                                    <SelectItem value="cli_command">
                                                                        CLI
                                                                        Command
                                                                    </SelectItem>
                                                                    <SelectItem value="custom">
                                                                        Custom
                                                                    </SelectItem>
                                                                </SelectContent>
                                                            </Select>
                                                        </div>
                                                    </div>

                                                    <div className="grid gap-2">
                                                        <Label
                                                            htmlFor={`install-path-${index}`}
                                                        >
                                                            Config Path /
                                                            Command (optional)
                                                        </Label>
                                                        <Input
                                                            id={`install-path-${index}`}
                                                            type="text"
                                                            value={
                                                                install.config_path
                                                            }
                                                            onChange={(e) =>
                                                                updateInstall(
                                                                    index,
                                                                    'config_path',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder={
                                                                install.install_method ===
                                                                'cli_command'
                                                                    ? 'npm install @package/config'
                                                                    : '/path/to/config.json'
                                                            }
                                                        />
                                                    </div>

                                                    <div className="grid gap-2">
                                                        <Label
                                                            htmlFor={`install-instructions-${index}`}
                                                        >
                                                            Instructions
                                                            (optional)
                                                        </Label>
                                                        <textarea
                                                            id={`install-instructions-${index}`}
                                                            value={
                                                                install.instructions
                                                            }
                                                            onChange={(e) =>
                                                                updateInstall(
                                                                    index,
                                                                    'instructions',
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            placeholder="Any specific steps to follow..."
                                                            className="flex min-h-[80px] w-full border border-ds-border bg-ds-bg-secondary px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                                        />
                                                    </div>
                                                </div>
                                            ),
                                        )}
                                    </div>
                                )}

                                {/* Files */}
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                            Config Files
                                        </h2>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={addFile}
                                            className="border-ds-border"
                                        >
                                            <Plus className="mr-2 h-4 w-4" />
                                            Add File
                                        </Button>
                                    </div>

                                    {data.files.map((file, index) => (
                                        <div
                                            key={index}
                                            className="space-y-4 border-2 border-ds-border bg-ds-bg-card p-4"
                                        >
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm text-ds-text-muted">
                                                    File {index + 1}
                                                    {index === 0 &&
                                                        ' (primary)'}
                                                </span>
                                                {data.files.length > 1 && (
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        onClick={() =>
                                                            removeFile(index)
                                                        }
                                                        className="text-red-500 hover:text-red-400"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </Button>
                                                )}
                                            </div>

                                            <div className="grid gap-4 md:grid-cols-3">
                                                <div className="grid gap-2">
                                                    <Label
                                                        htmlFor={`filename-${index}`}
                                                    >
                                                        Filename
                                                    </Label>
                                                    <Input
                                                        id={`filename-${index}`}
                                                        type="text"
                                                        value={file.filename}
                                                        onChange={(e) =>
                                                            updateFile(
                                                                index,
                                                                'filename',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="config.json"
                                                        required
                                                    />
                                                </div>

                                                <div className="grid gap-2">
                                                    <Label
                                                        htmlFor={`language-${index}`}
                                                    >
                                                        Language
                                                    </Label>
                                                    <Select
                                                        value={file.language}
                                                        onValueChange={(
                                                            value,
                                                        ) =>
                                                            updateFile(
                                                                index,
                                                                'language',
                                                                value,
                                                            )
                                                        }
                                                    >
                                                        <SelectTrigger>
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {languageOptions.map(
                                                                (lang) => (
                                                                    <SelectItem
                                                                        key={
                                                                            lang.value
                                                                        }
                                                                        value={
                                                                            lang.value
                                                                        }
                                                                    >
                                                                        {
                                                                            lang.label
                                                                        }
                                                                    </SelectItem>
                                                                ),
                                                            )}
                                                        </SelectContent>
                                                    </Select>
                                                </div>

                                                <div className="grid gap-2">
                                                    <Label
                                                        htmlFor={`path-${index}`}
                                                    >
                                                        Path (optional)
                                                    </Label>
                                                    <Input
                                                        id={`path-${index}`}
                                                        type="text"
                                                        value={file.path}
                                                        onChange={(e) =>
                                                            updateFile(
                                                                index,
                                                                'path',
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder=".config/"
                                                    />
                                                </div>
                                            </div>

                                            <div className="grid gap-2">
                                                <Label
                                                    htmlFor={`content-${index}`}
                                                >
                                                    Content
                                                </Label>
                                                <textarea
                                                    id={`content-${index}`}
                                                    value={file.content}
                                                    onChange={(e) =>
                                                        updateFile(
                                                            index,
                                                            'content',
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Paste your config content here..."
                                                    className="flex min-h-[200px] w-full border border-ds-border bg-ds-bg-secondary px-3 py-2 font-mono text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                                    required
                                                />
                                            </div>
                                        </div>
                                    ))}
                                    <InputError message={errors.files} />
                                </div>

                                {/* Source */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Source (optional)
                                    </h2>

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="github_url">
                                                GitHub URL
                                            </Label>
                                            <Input
                                                id="github_url"
                                                type="url"
                                                value={data.github_url}
                                                onChange={(e) =>
                                                    setData(
                                                        'github_url',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="https://github.com/username/repo"
                                            />
                                        </div>

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
                                                placeholder="https://example.com/config"
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
                                    <Button type="submit" disabled={processing}>
                                        {processing && <Spinner />}
                                        Submit Config
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
