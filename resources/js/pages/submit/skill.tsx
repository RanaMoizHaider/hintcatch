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
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';

interface SkillFile {
    filename: string;
    content: string;
    language: string;
    path: string;
}

export default function SubmitSkill() {
    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        description: string;
        license: string;
        readme: string;
        files: SkillFile[];
        source_url: string;
        source_author: string;
        github_url: string;
    }>({
        name: '',
        description: '',
        license: 'MIT',
        readme: '',
        files: [
            {
                filename: 'SKILL.md',
                content: '',
                language: 'markdown',
                path: '',
            },
        ],
        source_url: '',
        source_author: '',
        github_url: '',
    });

    const addFile = () => {
        setData('files', [
            ...data.files,
            { filename: '', content: '', language: 'markdown', path: '' },
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
        field: keyof SkillFile,
        value: string,
    ) => {
        const newFiles = [...data.files];
        newFiles[index] = { ...newFiles[index], [field]: value };
        setData('files', newFiles);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/submit/skill');
    };

    const languageOptions = [
        { value: 'markdown', label: 'Markdown' },
        { value: 'text', label: 'Plain Text' },
        { value: 'json', label: 'JSON' },
        { value: 'yaml', label: 'YAML' },
        { value: 'javascript', label: 'JavaScript' },
        { value: 'typescript', label: 'TypeScript' },
        { value: 'python', label: 'Python' },
        { value: 'bash', label: 'Bash' },
    ];

    return (
        <>
            <Head title="Submit Agent Skill" />
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
                                Submit Agent Skill
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Share a reusable skill following the
                                AgentSkills.io specification
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
                                        <Label htmlFor="name">Skill Name</Label>
                                        <Input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData('name', e.target.value)
                                            }
                                            placeholder="Code Review"
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
                                            placeholder="A brief description of what this skill does..."
                                            className="flex min-h-[100px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError
                                            message={errors.description}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="license">License</Label>
                                        <Select
                                            value={data.license}
                                            onValueChange={(value) =>
                                                setData('license', value)
                                            }
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="MIT">
                                                    MIT
                                                </SelectItem>
                                                <SelectItem value="Apache-2.0">
                                                    Apache 2.0
                                                </SelectItem>
                                                <SelectItem value="GPL-3.0">
                                                    GPL 3.0
                                                </SelectItem>
                                                <SelectItem value="BSD-3-Clause">
                                                    BSD 3-Clause
                                                </SelectItem>
                                                <SelectItem value="CC-BY-4.0">
                                                    CC BY 4.0
                                                </SelectItem>
                                                <SelectItem value="Unlicense">
                                                    Unlicense
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <InputError message={errors.license} />
                                    </div>
                                </div>

                                {/* README */}
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
                                        placeholder="# Installation&#10;&#10;Describe how to install and use this skill..."
                                        minHeight="200px"
                                    />
                                    <InputError message={errors.readme} />
                                </div>

                                {/* Skill Files */}
                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                            Skill Files
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
                                                        placeholder="SKILL.md"
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
                                                        placeholder=".skills/my-skill/"
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
                                                    placeholder={`---
name: ${data.name || 'My Skill'}
description: ${data.description || 'Skill description'}
license: ${data.license}
---

## When to use this skill

Describe when this skill should be invoked...

## How to use this skill

Step-by-step instructions for the AI agent...

## Keywords
keyword1, keyword2, keyword3`}
                                                    className="flex min-h-[300px] w-full border border-ds-border bg-ds-bg-secondary px-3 py-2 font-mono text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
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
                                            <InputError
                                                message={errors.github_url}
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
                                                placeholder="https://example.com/skill"
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
                                        Submit Skill
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
