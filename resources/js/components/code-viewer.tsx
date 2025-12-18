import { cn } from '@/lib/utils';
import type { ConfigFile } from '@/types/models';
import { Check, Copy } from 'lucide-react';
import { useState } from 'react';

interface CodeViewerProps {
    files: ConfigFile[];
}

export function CodeViewer({ files }: CodeViewerProps) {
    const [activeFile, setActiveFile] = useState(
        files.find((f) => f.is_primary) ?? files[0],
    );
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        if (!activeFile) return;
        await navigator.clipboard.writeText(activeFile.content);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    if (!activeFile) return null;

    return (
        <div className="border-2 border-ds-border bg-ds-bg-card">
            {/* File Tabs */}
            {files.length > 1 && (
                <div className="flex border-b-2 border-ds-border">
                    {files.map((file) => (
                        <button
                            key={file.id}
                            type="button"
                            onClick={() => setActiveFile(file)}
                            className={cn(
                                'border-r-2 border-ds-border px-4 py-2 text-xs transition-colors',
                                activeFile.id === file.id
                                    ? 'bg-ds-bg-secondary text-ds-text-primary'
                                    : 'text-ds-text-muted hover:bg-ds-bg-secondary hover:text-ds-text-primary',
                            )}
                        >
                            {file.filename}
                        </button>
                    ))}
                </div>
            )}

            {/* Header with file info and copy button */}
            <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                <div className="flex items-center gap-2">
                    <span className="text-xs text-ds-text-muted">
                        {activeFile.filename}
                    </span>
                    {activeFile.path && (
                        <span className="text-xs text-ds-text-muted">
                            ({activeFile.path})
                        </span>
                    )}
                </div>
                <button
                    type="button"
                    onClick={handleCopy}
                    className="flex items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                >
                    {copied ? (
                        <>
                            <Check className="h-3 w-3" />
                            Copied
                        </>
                    ) : (
                        <>
                            <Copy className="h-3 w-3" />
                            Copy
                        </>
                    )}
                </button>
            </div>

            {/* Code content */}
            <div className="overflow-x-auto">
                <pre className="p-4 text-sm leading-relaxed text-ds-text-primary">
                    <code>{activeFile.content}</code>
                </pre>
            </div>
        </div>
    );
}
