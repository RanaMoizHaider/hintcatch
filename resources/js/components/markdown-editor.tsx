import { cn } from '@/lib/utils';
import { Eye, Pencil } from 'lucide-react';
import { useState } from 'react';
import { MarkdownRenderer } from './markdown-renderer';

interface MarkdownEditorProps {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
    minHeight?: string;
    id?: string;
    required?: boolean;
}

export function MarkdownEditor({
    value,
    onChange,
    placeholder = 'Write your content here...',
    minHeight = '200px',
    id,
    required,
}: MarkdownEditorProps) {
    const [activeTab, setActiveTab] = useState<'write' | 'preview'>('write');

    return (
        <div className="overflow-hidden border border-ds-border bg-ds-bg-elevated">
            <div className="flex border-b border-ds-border">
                <button
                    type="button"
                    onClick={() => setActiveTab('write')}
                    className={cn(
                        'flex items-center gap-1.5 px-3 py-2 text-sm transition-colors',
                        activeTab === 'write'
                            ? 'border-b-2 border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                            : 'text-ds-text-muted hover:text-ds-text-secondary',
                    )}
                >
                    <Pencil className="h-3.5 w-3.5" />
                    Write
                </button>
                <button
                    type="button"
                    onClick={() => setActiveTab('preview')}
                    className={cn(
                        'flex items-center gap-1.5 px-3 py-2 text-sm transition-colors',
                        activeTab === 'preview'
                            ? 'border-b-2 border-ds-text-primary bg-ds-bg-secondary text-ds-text-primary'
                            : 'text-ds-text-muted hover:text-ds-text-secondary',
                    )}
                >
                    <Eye className="h-3.5 w-3.5" />
                    Preview
                </button>
            </div>

            {activeTab === 'write' ? (
                <textarea
                    id={id}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    placeholder={placeholder}
                    required={required}
                    style={{ minHeight }}
                    className="w-full resize-y bg-ds-bg-secondary px-3 py-2 font-mono text-sm text-ds-text-primary outline-none placeholder:text-ds-text-muted"
                />
            ) : (
                <div
                    style={{ minHeight }}
                    className="bg-ds-bg-secondary px-4 py-3"
                >
                    {value ? (
                        <MarkdownRenderer content={value} />
                    ) : (
                        <p className="text-sm text-ds-text-muted italic">
                            Nothing to preview
                        </p>
                    )}
                </div>
            )}
        </div>
    );
}
