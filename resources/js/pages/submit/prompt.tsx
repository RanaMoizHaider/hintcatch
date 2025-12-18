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
import type { SubmitPromptPageProps } from '@/types/models';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

type PromptCategory =
    | 'system'
    | 'task'
    | 'review'
    | 'documentation'
    | 'debugging'
    | 'refactoring';

const PROMPT_CATEGORIES: { value: PromptCategory; label: string }[] = [
    { value: 'system', label: 'System' },
    { value: 'task', label: 'Task' },
    { value: 'review', label: 'Review' },
    { value: 'documentation', label: 'Documentation' },
    { value: 'debugging', label: 'Debugging' },
    { value: 'refactoring', label: 'Refactoring' },
];

export default function SubmitPrompt({}: SubmitPromptPageProps) {
    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        description: string;
        content: string;
        category: PromptCategory | '';
        source_url: string;
        source_author: string;
    }>({
        name: '',
        description: '',
        content: '',
        category: '',
        source_url: '',
        source_author: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/submit/prompt');
    };

    return (
        <>
            <Head title="Submit Prompt" />
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
                                Submit Prompt
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Share a reusable prompt with the community
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
                                            placeholder="My Awesome Prompt"
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
                                            placeholder="Briefly describe what this prompt does and when to use it..."
                                            className="flex min-h-[80px] w-full border border-ds-border bg-ds-bg-card px-3 py-2 text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError
                                            message={errors.description}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="category">
                                            Category
                                        </Label>
                                        <Select
                                            value={data.category}
                                            onValueChange={(
                                                value: PromptCategory,
                                            ) => setData('category', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {PROMPT_CATEGORIES.map(
                                                    (category) => (
                                                        <SelectItem
                                                            key={category.value}
                                                            value={
                                                                category.value
                                                            }
                                                        >
                                                            {category.label}
                                                        </SelectItem>
                                                    ),
                                                )}
                                            </SelectContent>
                                        </Select>
                                        <p className="text-xs text-ds-text-muted">
                                            Choose the category that best
                                            describes your prompt's purpose
                                        </p>
                                        <InputError message={errors.category} />
                                    </div>
                                </div>

                                {/* Prompt Content */}
                                <div className="space-y-4">
                                    <h2 className="text-sm font-medium text-ds-text-muted uppercase">
                                        Prompt Content
                                    </h2>

                                    <div className="grid gap-2">
                                        <Label htmlFor="content">Content</Label>
                                        <textarea
                                            id="content"
                                            value={data.content}
                                            onChange={(e) =>
                                                setData(
                                                    'content',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Enter your prompt content here...

You can use markdown formatting.

Example:
# Task
You are a helpful assistant that...

# Guidelines
- Be concise
- Use clear language
- ..."
                                            className="flex min-h-[300px] w-full border border-ds-border bg-ds-bg-secondary px-3 py-2 font-mono text-sm text-ds-text-primary shadow-xs outline-none placeholder:text-ds-text-muted focus-visible:border-white focus-visible:ring-[3px] focus-visible:ring-white/20"
                                            required
                                        />
                                        <InputError message={errors.content} />
                                    </div>
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
                                        Submit Prompt
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
