import { cn } from '@/lib/utils';
import { Check, Copy } from 'lucide-react';
import { useState } from 'react';

export interface FileViewerFile {
    id: string | number;
    filename: string;
    content: string;
    path?: string | null;
    language?: string;
    isPrimary?: boolean;
}

interface FileViewerProps {
    file: FileViewerFile;
    className?: string;
    showHeader?: boolean;
}

export function FileViewer({
    file,
    className,
    showHeader = true,
}: FileViewerProps) {
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        await navigator.clipboard.writeText(file.content);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div
            className={cn('border-2 border-ds-border bg-ds-bg-card', className)}
        >
            {showHeader && (
                <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                    <div className="flex items-center gap-2">
                        <span className="text-xs text-ds-text-muted">
                            {file.filename}
                        </span>
                        {file.path && (
                            <span className="text-xs text-ds-text-muted">
                                ({file.path})
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
            )}

            <div className="overflow-x-auto">
                <pre className="p-4 text-sm leading-relaxed text-ds-text-primary">
                    <code>{file.content}</code>
                </pre>
            </div>
        </div>
    );
}
