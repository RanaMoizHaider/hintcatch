import { cn } from '@/lib/utils';
import { Check, Copy } from 'lucide-react';
import { useState } from 'react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';

interface MarkdownRendererProps {
    content: string;
    className?: string;
}

function CodeBlock({
    children,
    className,
}: {
    children: string;
    className?: string;
}) {
    const [copied, setCopied] = useState(false);
    const language = className?.replace('language-', '') || '';

    const handleCopy = async () => {
        await navigator.clipboard.writeText(children);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    return (
        <div className="border-2 border-ds-border bg-ds-bg-card">
            <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                <span className="text-xs text-ds-text-muted">
                    {language || 'code'}
                </span>
                <button
                    type="button"
                    onClick={handleCopy}
                    className="flex items-center gap-1.5 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                >
                    {copied ? (
                        <>
                            <Check className="h-3.5 w-3.5" />
                            <span>Copied</span>
                        </>
                    ) : (
                        <>
                            <Copy className="h-3.5 w-3.5" />
                            <span>Copy</span>
                        </>
                    )}
                </button>
            </div>
            <div className="overflow-x-auto bg-ds-bg-secondary">
                <pre className="p-4">
                    <code className="text-sm leading-relaxed text-ds-text-primary">
                        {children}
                    </code>
                </pre>
            </div>
        </div>
    );
}

export function MarkdownRenderer({
    content,
    className = '',
}: MarkdownRendererProps) {
    return (
        <div className={cn('space-y-4', className)}>
            <ReactMarkdown
                remarkPlugins={[remarkGfm]}
                components={{
                    h1: ({ children }) => (
                        <h1 className="text-xl font-semibold text-ds-text-primary">
                            {children}
                        </h1>
                    ),
                    h2: ({ children }) => (
                        <h2 className="text-lg font-semibold text-ds-text-primary">
                            {children}
                        </h2>
                    ),
                    h3: ({ children }) => (
                        <h3 className="text-base font-semibold text-ds-text-primary">
                            {children}
                        </h3>
                    ),
                    p: ({ children }) => (
                        <p className="text-sm leading-relaxed text-ds-text-secondary">
                            {children}
                        </p>
                    ),
                    ul: ({ children }) => (
                        <ul className="list-inside list-disc space-y-1 text-sm text-ds-text-secondary">
                            {children}
                        </ul>
                    ),
                    ol: ({ children }) => (
                        <ol className="list-inside list-decimal space-y-1 text-sm text-ds-text-secondary">
                            {children}
                        </ol>
                    ),
                    li: ({ children }) => (
                        <li className="text-sm text-ds-text-secondary">
                            {children}
                        </li>
                    ),
                    a: ({ href, children }) => (
                        <a
                            href={href}
                            className="text-ds-success underline-offset-2 hover:underline"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            {children}
                        </a>
                    ),
                    strong: ({ children }) => (
                        <strong className="font-semibold text-ds-text-primary">
                            {children}
                        </strong>
                    ),
                    em: ({ children }) => (
                        <em className="text-ds-text-secondary italic">
                            {children}
                        </em>
                    ),
                    pre: ({ children, node }) => {
                        const codeNode = node?.children?.[0] as {
                            properties?: { className?: string[] };
                            children?: { value?: string }[];
                        };
                        const className =
                            codeNode?.properties?.className?.[0] || '';
                        const language =
                            String(className).replace('language-', '') || '';
                        const codeContent =
                            codeNode?.children?.[0]?.value || '';
                        const content = String(codeContent).replace(/\n$/, '');

                        return (
                            <CodeBlock className={language}>
                                {content}
                            </CodeBlock>
                        );
                    },
                    code: ({ className, children }) => {
                        const content = String(children).replace(/\n$/, '');
                        return (
                            <code className="rounded bg-ds-bg-secondary px-1.5 py-0.5 font-mono text-sm text-ds-text-primary">
                                {content}
                            </code>
                        );
                    },
                    blockquote: ({ children }) => (
                        <blockquote className="border-l-2 border-ds-border pl-4 text-sm text-ds-text-muted italic">
                            {children}
                        </blockquote>
                    ),
                    hr: () => <hr className="border-ds-border" />,
                    table: ({ children }) => (
                        <div className="overflow-x-auto">
                            <table className="w-full border-2 border-ds-border text-sm">
                                {children}
                            </table>
                        </div>
                    ),
                    thead: ({ children }) => (
                        <thead className="border-b-2 border-ds-border bg-ds-bg-secondary">
                            {children}
                        </thead>
                    ),
                    th: ({ children }) => (
                        <th className="px-4 py-2 text-left font-semibold text-ds-text-primary">
                            {children}
                        </th>
                    ),
                    td: ({ children }) => (
                        <td className="border-t border-ds-border px-4 py-2 text-ds-text-secondary">
                            {children}
                        </td>
                    ),
                }}
            >
                {content}
            </ReactMarkdown>
        </div>
    );
}
