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
import type { SubmitSkillPageProps } from '@/types/models';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

export default function SubmitSkill({ categories }: SubmitSkillPageProps) {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const { data, setData, errors, setError } = useForm<{
        name: string;
        description: string;
        content: string;
        category_id: string;
        license: string;
        allowed_tools: string[];
        source_url: string;
        source_author: string;
        github_url: string;
    }>({
        name: '',
        description: '',
        content: '',
        category_id: '',
        license: 'MIT',
        allowed_tools: [''],
        source_url: '',
        source_author: '',
        github_url: '',
    });

    const addAllowedTool = () => {
        setData('allowed_tools', [...data.allowed_tools, '']);
    };

    const removeAllowedTool = (index: number) => {
        if (data.allowed_tools.length > 1) {
            setData(
                'allowed_tools',
                data.allowed_tools.filter((_, i) => i !== index),
            );
        }
    };

    const updateAllowedTool = (index: number, value: string) => {
        const newTools = [...data.allowed_tools];
        newTools[index] = value;
        setData('allowed_tools', newTools);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        const submitData = {
            name: data.name,
            description: data.description,
            content: data.content,
            category_id: data.category_id || null,
            license: data.license || null,
            allowed_tools: data.allowed_tools.filter((t) => t.trim() !== ''),
            source_url: data.source_url || null,
            source_author: data.source_author || null,
            github_url: data.github_url || null,
        };

        router.post('/submit/skill', submitData, {
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
                                            className="flex min-h-[80px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError
                                            message={errors.description}
                                        />
                                    </div>

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="grid gap-2">
                                            <Label htmlFor="category">
                                                Category
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
                                                    <SelectValue placeholder="Select category" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {categories.map(
                                                        (category) => (
                                                            <SelectItem
                                                                key={
                                                                    category.id
                                                                }
                                                                value={String(
                                                                    category.id,
                                                                )}
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

                                        <div className="grid gap-2">
                                            <Label htmlFor="license">
                                                License
                                            </Label>
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
                                            <InputError
                                                message={errors.license}
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Skill Content (Markdown)
                                    </h2>

                                    <div className="grid gap-2">
                                        <Label htmlFor="content">
                                            Instructions
                                        </Label>
                                        <textarea
                                            id="content"
                                            value={data.content}
                                            onChange={(e) =>
                                                setData(
                                                    'content',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={`## When to use this skill

Describe when this skill should be invoked...

## How to use this skill

Step-by-step instructions for the AI agent...

## Keywords
keyword1, keyword2, keyword3`}
                                            className="flex min-h-[300px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 font-mono text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <p className="text-xs text-ds-text-muted">
                                            Write the skill instructions in
                                            Markdown format following the
                                            AgentSkills.io spec
                                        </p>
                                        <InputError message={errors.content} />
                                    </div>
                                </div>

                                <div className="space-y-4">
                                    <div className="flex items-center justify-between">
                                        <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                            Allowed Tools (optional)
                                        </h2>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={addAllowedTool}
                                            className="border-ds-border"
                                        >
                                            <Plus className="mr-2 h-4 w-4" />
                                            Add Tool
                                        </Button>
                                    </div>
                                    <p className="text-xs text-ds-text-muted">
                                        Specify which tools this skill is
                                        allowed to use (e.g., bash, read, write,
                                        grep)
                                    </p>
                                    {data.allowed_tools.map((tool, index) => (
                                        <div
                                            key={index}
                                            className="flex items-center gap-2"
                                        >
                                            <Input
                                                type="text"
                                                value={tool}
                                                onChange={(e) =>
                                                    updateAllowedTool(
                                                        index,
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="bash"
                                            />
                                            {data.allowed_tools.length > 1 && (
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() =>
                                                        removeAllowedTool(index)
                                                    }
                                                    className="text-red-500 hover:text-red-400"
                                                >
                                                    <Trash2 className="h-4 w-4" />
                                                </Button>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Source (optional)
                                    </h2>

                                    <div className="grid gap-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="github_url">
                                                GitHub Repository
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
                                                placeholder="https://github.com/user/repo"
                                            />
                                            <InputError
                                                message={errors.github_url}
                                            />
                                        </div>

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
                                                    placeholder="https://..."
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
                                                    message={
                                                        errors.source_author
                                                    }
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
