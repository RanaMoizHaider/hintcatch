import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft, Plus, X } from 'lucide-react';
import { useState } from 'react';

import { index } from '@/actions/App/Http/Controllers/Admin/AgentController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import AdminLayout from '@/layouts/admin-layout';
import { Agent, ConfigType } from '@/types/models';

interface Props {
    agent?: Agent;
    configTypes: ConfigType[];
}

const FILE_FORMATS = ['json', 'jsonc', 'yaml', 'toml', 'md', 'ts', 'js', 'sh'];
const MCP_TRANSPORT_TYPES = ['stdio', 'sse', 'http', 'local', 'remote'];

function TagInput({
    value,
    onChange,
    suggestions,
    placeholder,
}: {
    value: string[];
    onChange: (tags: string[]) => void;
    suggestions?: string[];
    placeholder?: string;
}) {
    const [inputValue, setInputValue] = useState('');

    const addTag = (tag: string) => {
        const trimmed = tag.trim().toLowerCase();
        if (trimmed && !value.includes(trimmed)) {
            onChange([...value, trimmed]);
        }
        setInputValue('');
    };

    const removeTag = (index: number) => {
        onChange(value.filter((_, i) => i !== index));
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(inputValue);
        } else if (e.key === 'Backspace' && !inputValue && value.length > 0) {
            removeTag(value.length - 1);
        }
    };

    const availableSuggestions =
        suggestions?.filter((s) => !value.includes(s)) ?? [];

    return (
        <div className="space-y-2">
            <div className="ds-border ds-bg-base flex min-h-10 flex-wrap gap-1.5 rounded-none border p-2">
                {value.map((tag, index) => (
                    <Badge
                        key={tag}
                        variant="secondary"
                        className="gap-1 py-0.5 pr-1"
                    >
                        {tag}
                        <button
                            type="button"
                            onClick={() => removeTag(index)}
                            className="hover:ds-bg-base rounded-none p-0.5"
                        >
                            <X className="h-3 w-3" />
                        </button>
                    </Badge>
                ))}
                <input
                    type="text"
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                    onKeyDown={handleKeyDown}
                    onBlur={() => inputValue && addTag(inputValue)}
                    placeholder={value.length === 0 ? placeholder : ''}
                    className="min-w-[120px] flex-1 bg-transparent text-sm outline-none"
                />
            </div>
            {availableSuggestions.length > 0 && (
                <div className="flex flex-wrap gap-1">
                    {availableSuggestions.map((suggestion) => (
                        <button
                            key={suggestion}
                            type="button"
                            onClick={() => addTag(suggestion)}
                            className="ds-border hover:ds-bg-secondary flex items-center gap-1 border px-2 py-0.5 text-xs"
                        >
                            <Plus className="h-3 w-3" />
                            {suggestion}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

function JsonEditor({
    value,
    onChange,
    error,
}: {
    value: Record<string, unknown> | null;
    onChange: (val: Record<string, unknown> | null) => void;
    error?: string;
}) {
    const [text, setText] = useState(() =>
        value ? JSON.stringify(value, null, 2) : '',
    );
    const [parseError, setParseError] = useState<string | null>(null);

    const handleChange = (newText: string) => {
        setText(newText);
        if (!newText.trim()) {
            setParseError(null);
            onChange(null);
            return;
        }
        try {
            const parsed = JSON.parse(newText);
            setParseError(null);
            onChange(parsed);
        } catch {
            setParseError('Invalid JSON');
        }
    };

    return (
        <div className="space-y-1">
            <Textarea
                value={text}
                onChange={(e) => handleChange(e.target.value)}
                className="font-mono text-sm"
                rows={10}
            />
            {(parseError || error) && (
                <p className="text-sm text-red-500">{parseError || error}</p>
            )}
        </div>
    );
}

function KeyValueEditor({
    value,
    onChange,
    keyLabel = 'Key',
    valueLabel = 'Value',
}: {
    value: Record<string, string> | null;
    onChange: (val: Record<string, string> | null) => void;
    keyLabel?: string;
    valueLabel?: string;
}) {
    const entries = Object.entries(value ?? {});

    const updateEntry = (
        index: number,
        field: 'key' | 'value',
        newVal: string,
    ) => {
        const newEntries = [...entries];
        if (field === 'key') {
            newEntries[index] = [newVal, entries[index][1]];
        } else {
            newEntries[index] = [entries[index][0], newVal];
        }
        onChange(Object.fromEntries(newEntries.filter(([k]) => k)));
    };

    const addEntry = () => {
        onChange({ ...(value ?? {}), '': '' });
    };

    const removeEntry = (key: string) => {
        const newVal = { ...(value ?? {}) };
        delete newVal[key];
        onChange(Object.keys(newVal).length > 0 ? newVal : null);
    };

    return (
        <div className="space-y-2">
            {entries.map(([key, val], index) => (
                <div key={index} className="flex gap-2">
                    <Input
                        value={key}
                        onChange={(e) =>
                            updateEntry(index, 'key', e.target.value)
                        }
                        placeholder={keyLabel}
                        className="flex-1"
                    />
                    <Input
                        value={val}
                        onChange={(e) =>
                            updateEntry(index, 'value', e.target.value)
                        }
                        placeholder={valueLabel}
                        className="flex-1"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        onClick={() => removeEntry(key)}
                    >
                        <X className="h-4 w-4" />
                    </Button>
                </div>
            ))}
            <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addEntry}
            >
                <Plus className="mr-1 h-3 w-3" />
                Add
            </Button>
        </div>
    );
}

export default function AgentForm({ agent, configTypes }: Props) {
    const isEdit = !!agent;
    const title = isEdit ? `Edit ${agent.name}` : 'Create Agent';

    const configTypeSlugs = configTypes.map((ct) => ct.slug);

    const [formData, setFormData] = useState({
        name: agent?.name ?? '',
        slug: agent?.slug ?? '',
        description: agent?.description ?? '',
        website: agent?.website ?? '',
        docs_url: agent?.docs_url ?? '',
        github_url: agent?.github_url ?? '',
        logo: agent?.logo ?? '',
        rules_filename: agent?.rules_filename ?? '',
        supports_mcp: agent?.supports_mcp ?? false,
        supported_config_types: agent?.supported_config_types ?? [],
        supported_file_formats: agent?.supported_file_formats ?? [],
        mcp_transport_types: agent?.mcp_transport_types ?? [],
        mcp_config_paths: agent?.mcp_config_paths ?? null,
        mcp_config_template: agent?.mcp_config_template ?? null,
        skills_config_template: agent?.skills_config_template ?? null,
        config_type_templates: agent?.config_type_templates ?? null,
    });

    const updateField = <K extends keyof typeof formData>(
        key: K,
        value: (typeof formData)[K],
    ) => {
        setFormData((prev) => ({ ...prev, [key]: value }));
    };

    const generateSlug = () => {
        const slug = formData.name
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        updateField('slug', slug);
    };

    return (
        <AdminLayout>
            <Head title={title} />

            <div className="mb-6 flex items-center gap-4">
                <Link href={index.url()}>
                    <Button variant="ghost" size="icon">
                        <ArrowLeft className="h-4 w-4" />
                    </Button>
                </Link>
                <h1 className="text-2xl font-semibold">{title}</h1>
            </div>

            <Form
                action={isEdit ? `/admin/agents/${agent.id}` : '/admin/agents'}
                method={isEdit ? 'put' : 'post'}
                data={formData}
            >
                {({ errors, processing }) => (
                    <div className="space-y-8">
                        <section className="ds-border ds-bg-card border p-6">
                            <h2 className="mb-4 text-lg font-medium">
                                Basic Information
                            </h2>
                            <div className="grid grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="name">Name *</Label>
                                    <Input
                                        id="name"
                                        value={formData.name}
                                        onChange={(e) =>
                                            updateField('name', e.target.value)
                                        }
                                        onBlur={() =>
                                            !formData.slug && generateSlug()
                                        }
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-500">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="slug">Slug *</Label>
                                    <div className="flex gap-2">
                                        <Input
                                            id="slug"
                                            value={formData.slug}
                                            onChange={(e) =>
                                                updateField(
                                                    'slug',
                                                    e.target.value,
                                                )
                                            }
                                            className="flex-1"
                                        />
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={generateSlug}
                                        >
                                            Generate
                                        </Button>
                                    </div>
                                    {errors.slug && (
                                        <p className="text-sm text-red-500">
                                            {errors.slug}
                                        </p>
                                    )}
                                </div>

                                <div className="col-span-2 space-y-2">
                                    <Label htmlFor="description">
                                        Description
                                    </Label>
                                    <Textarea
                                        id="description"
                                        value={formData.description}
                                        onChange={(e) =>
                                            updateField(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        rows={3}
                                    />
                                    {errors.description && (
                                        <p className="text-sm text-red-500">
                                            {errors.description}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="logo">Logo URL</Label>
                                    <div className="flex gap-3">
                                        {formData.logo && (
                                            <img
                                                src={formData.logo}
                                                alt=""
                                                className="ds-border h-10 w-10 border object-contain"
                                            />
                                        )}
                                        <Input
                                            id="logo"
                                            type="url"
                                            value={formData.logo}
                                            onChange={(e) =>
                                                updateField(
                                                    'logo',
                                                    e.target.value,
                                                )
                                            }
                                            className="flex-1"
                                        />
                                    </div>
                                    {errors.logo && (
                                        <p className="text-sm text-red-500">
                                            {errors.logo}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="rules_filename">
                                        Rules Filename
                                    </Label>
                                    <Input
                                        id="rules_filename"
                                        value={formData.rules_filename}
                                        onChange={(e) =>
                                            updateField(
                                                'rules_filename',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="e.g., AGENTS.md, .cursorrules"
                                    />
                                    {errors.rules_filename && (
                                        <p className="text-sm text-red-500">
                                            {errors.rules_filename}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </section>

                        <section className="ds-border ds-bg-card border p-6">
                            <h2 className="mb-4 text-lg font-medium">URLs</h2>
                            <div className="grid grid-cols-3 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="website">Website</Label>
                                    <Input
                                        id="website"
                                        type="url"
                                        value={formData.website}
                                        onChange={(e) =>
                                            updateField(
                                                'website',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    {errors.website && (
                                        <p className="text-sm text-red-500">
                                            {errors.website}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="docs_url">
                                        Documentation URL
                                    </Label>
                                    <Input
                                        id="docs_url"
                                        type="url"
                                        value={formData.docs_url}
                                        onChange={(e) =>
                                            updateField(
                                                'docs_url',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    {errors.docs_url && (
                                        <p className="text-sm text-red-500">
                                            {errors.docs_url}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="github_url">
                                        GitHub URL
                                    </Label>
                                    <Input
                                        id="github_url"
                                        type="url"
                                        value={formData.github_url}
                                        onChange={(e) =>
                                            updateField(
                                                'github_url',
                                                e.target.value,
                                            )
                                        }
                                    />
                                    {errors.github_url && (
                                        <p className="text-sm text-red-500">
                                            {errors.github_url}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </section>

                        <section className="ds-border ds-bg-card border p-6">
                            <h2 className="mb-4 text-lg font-medium">
                                Supported Configuration
                            </h2>
                            <div className="grid grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label>Supported Config Types</Label>
                                    <TagInput
                                        value={formData.supported_config_types}
                                        onChange={(val) =>
                                            updateField(
                                                'supported_config_types',
                                                val,
                                            )
                                        }
                                        suggestions={configTypeSlugs}
                                        placeholder="Add config types..."
                                    />
                                    {errors.supported_config_types && (
                                        <p className="text-sm text-red-500">
                                            {errors.supported_config_types}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label>Supported File Formats</Label>
                                    <TagInput
                                        value={formData.supported_file_formats}
                                        onChange={(val) =>
                                            updateField(
                                                'supported_file_formats',
                                                val,
                                            )
                                        }
                                        suggestions={FILE_FORMATS}
                                        placeholder="Add file formats..."
                                    />
                                    {errors.supported_file_formats && (
                                        <p className="text-sm text-red-500">
                                            {errors.supported_file_formats}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </section>

                        <section className="ds-border ds-bg-card border p-6">
                            <div className="mb-4 flex items-center justify-between">
                                <h2 className="text-lg font-medium">
                                    MCP Configuration
                                </h2>
                                <div className="flex items-center gap-2">
                                    <Switch
                                        id="supports_mcp"
                                        checked={formData.supports_mcp}
                                        onCheckedChange={(checked) =>
                                            updateField('supports_mcp', checked)
                                        }
                                    />
                                    <Label htmlFor="supports_mcp">
                                        Supports MCP
                                    </Label>
                                </div>
                            </div>

                            {formData.supports_mcp && (
                                <div className="space-y-6">
                                    <div className="space-y-2">
                                        <Label>MCP Transport Types</Label>
                                        <TagInput
                                            value={
                                                formData.mcp_transport_types ??
                                                []
                                            }
                                            onChange={(val) =>
                                                updateField(
                                                    'mcp_transport_types',
                                                    val,
                                                )
                                            }
                                            suggestions={MCP_TRANSPORT_TYPES}
                                            placeholder="Add transport types..."
                                        />
                                        {errors.mcp_transport_types && (
                                            <p className="text-sm text-red-500">
                                                {errors.mcp_transport_types}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label>MCP Config Paths</Label>
                                        <KeyValueEditor
                                            value={
                                                formData.mcp_config_paths as Record<
                                                    string,
                                                    string
                                                > | null
                                            }
                                            onChange={(val) =>
                                                updateField(
                                                    'mcp_config_paths',
                                                    val,
                                                )
                                            }
                                            keyLabel="Scope (project/global)"
                                            valueLabel="Path"
                                        />
                                        {errors.mcp_config_paths && (
                                            <p className="text-sm text-red-500">
                                                {errors.mcp_config_paths}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label>
                                            MCP Config Template (JSON)
                                        </Label>
                                        <JsonEditor
                                            value={formData.mcp_config_template}
                                            onChange={(val) =>
                                                updateField(
                                                    'mcp_config_template',
                                                    val,
                                                )
                                            }
                                            error={errors.mcp_config_template}
                                        />
                                    </div>
                                </div>
                            )}
                        </section>

                        <section className="ds-border ds-bg-card border p-6">
                            <h2 className="mb-4 text-lg font-medium">
                                Skills Configuration
                            </h2>
                            <div className="space-y-2">
                                <Label>Skills Config Template (JSON)</Label>
                                <JsonEditor
                                    value={formData.skills_config_template}
                                    onChange={(val) =>
                                        updateField(
                                            'skills_config_template',
                                            val,
                                        )
                                    }
                                    error={errors.skills_config_template}
                                />
                            </div>
                        </section>

                        <section className="ds-border ds-bg-card border p-6">
                            <h2 className="mb-4 text-lg font-medium">
                                Config Type Templates
                            </h2>
                            <div className="space-y-2">
                                <Label>Config Type Templates (JSON)</Label>
                                <JsonEditor
                                    value={formData.config_type_templates}
                                    onChange={(val) =>
                                        updateField(
                                            'config_type_templates',
                                            val,
                                        )
                                    }
                                    error={errors.config_type_templates}
                                />
                            </div>
                        </section>

                        <div className="flex justify-end gap-3">
                            <Link href={index.url()}>
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" disabled={processing}>
                                {processing
                                    ? 'Saving...'
                                    : isEdit
                                      ? 'Update Agent'
                                      : 'Create Agent'}
                            </Button>
                        </div>
                    </div>
                )}
            </Form>
        </AdminLayout>
    );
}
