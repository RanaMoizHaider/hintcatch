import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Plus, X } from 'lucide-react';
import { type ReactNode, useState } from 'react';

interface FormSectionProps {
    title: string;
    description?: string;
    children: ReactNode;
}

export function FormSection({
    title,
    description,
    children,
}: FormSectionProps) {
    return (
        <div className="border border-ds-border bg-ds-bg-card p-6">
            <div className="mb-4">
                <h3 className="text-lg font-semibold text-ds-text-primary">
                    {title}
                </h3>
                {description && (
                    <p className="text-sm text-ds-text-muted">{description}</p>
                )}
            </div>
            <div className="space-y-4">{children}</div>
        </div>
    );
}

interface FormFieldProps {
    label: string;
    name: string;
    error?: string;
    children: ReactNode;
    description?: string;
}

export function FormField({
    label,
    name,
    error,
    children,
    description,
}: FormFieldProps) {
    return (
        <div className="space-y-2">
            <Label htmlFor={name} className="text-ds-text-primary">
                {label}
            </Label>
            {children}
            {description && (
                <p className="text-xs text-ds-text-muted">{description}</p>
            )}
            {error && <p className="text-sm text-destructive">{error}</p>}
        </div>
    );
}

interface TagInputProps {
    name: string;
    value: string[];
    onChange: (value: string[]) => void;
    placeholder?: string;
    suggestions?: string[];
}

export function TagInput({
    name,
    value,
    onChange,
    placeholder = 'Add item...',
    suggestions = [],
}: TagInputProps) {
    const [input, setInput] = useState('');

    const addTag = (tag: string) => {
        const trimmed = tag.trim();
        if (trimmed && !value.includes(trimmed)) {
            onChange([...value, trimmed]);
        }
        setInput('');
    };

    const removeTag = (index: number) => {
        onChange(value.filter((_, i) => i !== index));
    };

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addTag(input);
        }
    };

    const availableSuggestions = suggestions.filter((s) => !value.includes(s));

    return (
        <div className="space-y-2">
            <input type="hidden" name={name} value={JSON.stringify(value)} />
            <div className="flex flex-wrap gap-2">
                {value.map((tag, index) => (
                    <Badge key={tag} variant="secondary" className="gap-1 pr-1">
                        {tag}
                        <button
                            type="button"
                            onClick={() => removeTag(index)}
                            className="ml-1 hover:text-destructive"
                        >
                            <X className="size-3" />
                        </button>
                    </Badge>
                ))}
            </div>
            <div className="flex gap-2">
                <Input
                    value={input}
                    onChange={(e) => setInput(e.target.value)}
                    onKeyDown={handleKeyDown}
                    placeholder={placeholder}
                    className="flex-1"
                />
                <Button
                    type="button"
                    variant="outline"
                    size="icon"
                    onClick={() => addTag(input)}
                    disabled={!input.trim()}
                >
                    <Plus className="size-4" />
                </Button>
            </div>
            {availableSuggestions.length > 0 && (
                <div className="flex flex-wrap gap-1">
                    {availableSuggestions.slice(0, 8).map((suggestion) => (
                        <button
                            key={suggestion}
                            type="button"
                            onClick={() => addTag(suggestion)}
                            className="border border-ds-border px-2 py-0.5 text-xs text-ds-text-muted transition-colors hover:bg-ds-bg-secondary hover:text-ds-text-primary"
                        >
                            + {suggestion}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

interface JsonEditorProps {
    name: string;
    value: Record<string, unknown> | null;
    onChange: (value: Record<string, unknown> | null) => void;
    rows?: number;
}

export function JsonEditor({
    name,
    value,
    onChange,
    rows = 10,
}: JsonEditorProps) {
    const [text, setText] = useState(() =>
        value ? JSON.stringify(value, null, 2) : '',
    );
    const [error, setError] = useState<string | null>(null);

    const handleChange = (newText: string) => {
        setText(newText);
        if (!newText.trim()) {
            setError(null);
            onChange(null);
            return;
        }
        try {
            const parsed = JSON.parse(newText);
            setError(null);
            onChange(parsed);
        } catch {
            setError('Invalid JSON');
        }
    };

    return (
        <div className="space-y-1">
            <input
                type="hidden"
                name={name}
                value={value ? JSON.stringify(value) : ''}
            />
            <Textarea
                value={text}
                onChange={(e) => handleChange(e.target.value)}
                rows={rows}
                className="font-mono text-sm"
                placeholder="{}"
            />
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
}

interface KeyValueEditorProps {
    name: string;
    value: Record<string, string> | null;
    onChange: (value: Record<string, string> | null) => void;
    keyPlaceholder?: string;
    valuePlaceholder?: string;
}

export function KeyValueEditor({
    name,
    value,
    onChange,
    keyPlaceholder = 'Key',
    valuePlaceholder = 'Value',
}: KeyValueEditorProps) {
    const entries = Object.entries(value || {});

    const addEntry = () => {
        onChange({ ...(value || {}), '': '' });
    };

    const updateEntry = (
        oldKey: string,
        newKey: string,
        newValue: string,
        index: number,
    ) => {
        const newEntries = [...entries];
        newEntries[index] = [newKey, newValue];
        onChange(Object.fromEntries(newEntries.filter(([k]) => k !== '')));
    };

    const removeEntry = (key: string) => {
        const newObj = { ...(value || {}) };
        delete newObj[key];
        onChange(Object.keys(newObj).length > 0 ? newObj : null);
    };

    return (
        <div className="space-y-2">
            <input
                type="hidden"
                name={name}
                value={value ? JSON.stringify(value) : ''}
            />
            {entries.map(([key, val], index) => (
                <div key={index} className="flex gap-2">
                    <Input
                        value={key}
                        onChange={(e) =>
                            updateEntry(key, e.target.value, val, index)
                        }
                        placeholder={keyPlaceholder}
                        className="flex-1"
                    />
                    <Input
                        value={val}
                        onChange={(e) =>
                            updateEntry(key, key, e.target.value, index)
                        }
                        placeholder={valuePlaceholder}
                        className="flex-1"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        onClick={() => removeEntry(key)}
                    >
                        <X className="size-4" />
                    </Button>
                </div>
            ))}
            <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={addEntry}
            >
                <Plus className="mr-1 size-4" />
                Add
            </Button>
        </div>
    );
}
