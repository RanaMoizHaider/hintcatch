import InputError from '@/components/input-error';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
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
import { useState } from 'react';

interface ConfigFile {
    filename: string;
    content: string;
    language: string;
    path: string;
}

export default function SubmitConfig({
    agents,
    configTypes,
}: SubmitConfigPageProps) {
    const [selectedConfigTypeId, setSelectedConfigTypeId] = useState<
        string | null
    >(null);

    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        description: string;
        agent_id: string;
        config_type_id: string;
        category_id: string;
        source_url: string;
        source_author: string;
        files: ConfigFile[];
    }>({
        name: '',
        description: '',
        agent_id: '',
        config_type_id: '',
        category_id: '',
        source_url: '',
        source_author: '',
        files: [{ filename: '', content: '', language: 'json', path: '' }],
    });

    const selectedConfigType = configTypes.find(
        (ct) => ct.id.toString() === selectedConfigTypeId,
    );
    const categories: Category[] = selectedConfigType?.categories ?? [];

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
        if (data.files.length > 1) {
            setData(
                'files',
                data.files.filter((_, i) => i !== index),
            );
        }
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

                                {/* Classification */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Classification
                                    </h2>

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="agent">Agent</Label>
                                            <Select
                                                value={data.agent_id}
                                                onValueChange={(value) =>
                                                    setData('agent_id', value)
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select an agent" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {agents.map((agent) => (
                                                        <SelectItem
                                                            key={agent.id}
                                                            value={agent.id.toString()}
                                                        >
                                                            {agent.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={errors.agent_id}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="config_type">
                                                Config Type
                                            </Label>
                                            <Select
                                                value={data.config_type_id}
                                                onValueChange={
                                                    handleConfigTypeChange
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a type" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {configTypes.map((ct) => (
                                                        <SelectItem
                                                            key={ct.id}
                                                            value={ct.id.toString()}
                                                        >
                                                            {ct.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={errors.config_type_id}
                                            />
                                        </div>
                                    </div>

                                    {categories.length > 0 && (
                                        <div className="grid gap-2">
                                            <Label htmlFor="category">
                                                Category (optional)
                                            </Label>
                                            <Select
                                                value={data.category_id}
                                                onValueChange={(value) =>
                                                    setData(
                                                        'category_id',
                                                        value,
                                                    )
                                                }
                                            >
                                                <SelectTrigger>
                                                    <SelectValue placeholder="Select a category" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {categories.map(
                                                        (category) => (
                                                            <SelectItem
                                                                key={
                                                                    category.id
                                                                }
                                                                value={category.id.toString()}
                                                            >
                                                                {category.name}
                                                            </SelectItem>
                                                        ),
                                                    )}
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                message={errors.category_id}
                                            />
                                        </div>
                                    )}
                                </div>

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
