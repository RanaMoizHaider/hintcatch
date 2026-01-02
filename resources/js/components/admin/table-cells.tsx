import { Badge } from '@/components/ui/badge';
import { Check, ExternalLink, X } from 'lucide-react';
import { type ReactNode } from 'react';

interface AvatarCellProps {
    image?: string | null;
    name: string;
    subtitle?: string;
    extra?: string;
}

export function AvatarCell({ image, name, subtitle, extra }: AvatarCellProps) {
    return (
        <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 shrink-0 items-center justify-center bg-ds-bg-secondary">
                {image ? (
                    <img src={image} alt={name} className="h-6 w-6" />
                ) : (
                    <span className="text-sm font-medium text-ds-text-muted">
                        {name.charAt(0).toUpperCase()}
                    </span>
                )}
            </div>
            <div className="min-w-0">
                <div className="font-medium text-ds-text-primary">{name}</div>
                {(subtitle || extra) && (
                    <div className="text-xs text-ds-text-muted">
                        {subtitle}
                        {extra && (
                            <span className="ml-2 text-ds-text-subtle">
                                • {extra}
                            </span>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}

interface BooleanBadgeProps {
    value: boolean;
    trueLabel?: string;
    falseLabel?: string;
    showIcon?: boolean;
}

export function BooleanBadge({
    value,
    trueLabel = 'Yes',
    falseLabel = 'No',
    showIcon = true,
}: BooleanBadgeProps) {
    if (value) {
        return (
            <Badge variant="secondary" className="gap-1">
                {showIcon && <Check className="size-3" />}
                {trueLabel}
            </Badge>
        );
    }
    return (
        <Badge variant="outline" className="gap-1 text-ds-text-subtle">
            {showIcon && <X className="size-3" />}
            {falseLabel}
        </Badge>
    );
}

interface BadgeListProps {
    items: string[] | null | undefined;
    maxVisible?: number;
}

export function BadgeList({ items, maxVisible = 3 }: BadgeListProps) {
    if (!items || items.length === 0) {
        return <span className="text-ds-text-subtle">—</span>;
    }

    return (
        <div className="flex flex-wrap gap-1">
            {items.slice(0, maxVisible).map((item) => (
                <Badge key={item} variant="outline" className="text-xs">
                    {item}
                </Badge>
            ))}
            {items.length > maxVisible && (
                <Badge variant="outline" className="text-xs">
                    +{items.length - maxVisible}
                </Badge>
            )}
        </div>
    );
}

interface LinksCellProps {
    website?: string | null;
    github?: string | null;
    docs?: string | null;
}

export function LinksCell({ website, github, docs }: LinksCellProps) {
    const hasLinks = website || github || docs;

    if (!hasLinks) {
        return <span className="text-ds-text-subtle">—</span>;
    }

    return (
        <div className="flex items-center gap-2">
            {website && (
                <a
                    href={website}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-ds-text-muted transition-colors hover:text-ds-text-primary"
                    title="Website"
                >
                    <ExternalLink className="size-4" />
                </a>
            )}
            {github && (
                <a
                    href={github}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-ds-text-muted transition-colors hover:text-ds-text-primary"
                    title="GitHub"
                >
                    <GithubIcon />
                </a>
            )}
            {docs && (
                <a
                    href={docs}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-ds-text-muted transition-colors hover:text-ds-text-primary"
                    title="Documentation"
                >
                    <DocsIcon />
                </a>
            )}
        </div>
    );
}

function GithubIcon() {
    return (
        <svg className="size-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
        </svg>
    );
}

function DocsIcon() {
    return (
        <svg
            className="size-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
            />
        </svg>
    );
}

interface TextCellProps {
    children: ReactNode;
    muted?: boolean;
}

export function TextCell({ children, muted }: TextCellProps) {
    return (
        <span className={muted ? 'text-ds-text-muted' : 'text-ds-text-primary'}>
            {children}
        </span>
    );
}

interface CountCellProps {
    count?: number;
    label?: string;
}

export function CountCell({ count = 0, label }: CountCellProps) {
    return (
        <span className="text-ds-text-muted">
            {count}
            {label && <span className="ml-1 text-xs">{label}</span>}
        </span>
    );
}
