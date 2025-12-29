import type { ConfigTypeTemplate } from '@/types/models';
import {
    Check,
    Copy,
    FileText,
    FolderOpen,
    Package,
    Terminal,
} from 'lucide-react';
import { useState } from 'react';

interface PluginInstallSectionProps {
    template: ConfigTypeTemplate;
    agentName: string;
}

export function PluginInstallSection({
    template,
    agentName,
}: PluginInstallSectionProps) {
    const [copiedCommand, setCopiedCommand] = useState(false);
    const [copiedNpm, setCopiedNpm] = useState(false);

    const copyToClipboard = (
        text: string,
        setter: (value: boolean) => void,
    ) => {
        navigator.clipboard.writeText(text);
        setter(true);
        setTimeout(() => setter(false), 2000);
    };

    const hasFilePaths = template.global_path || template.project_path;
    const hasCliCommand =
        template.install_method === 'cli' && template.install_command;
    const hasNpmInstall = template.npm_install;

    if (!hasFilePaths && !hasCliCommand && !hasNpmInstall) {
        return null;
    }

    return (
        <section className="border-b-2 border-ds-border">
            <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                <h2 className="mb-6 text-lg font-medium text-ds-text-primary">
                    How to Install
                </h2>

                <div className="space-y-8">
                    {hasFilePaths && (
                        <div className="space-y-4">
                            <div className="flex items-center gap-2 text-sm font-medium text-ds-text-secondary">
                                <FolderOpen className="h-4 w-4" />
                                <span>File Location</span>
                            </div>
                            <div className="flex flex-wrap gap-4">
                                {template.project_path && (
                                    <div className="space-y-1">
                                        <span className="text-xs text-ds-text-muted uppercase">
                                            Project
                                        </span>
                                        <code className="block border-2 border-ds-border bg-ds-bg-secondary/50 px-3 py-2 font-mono text-sm text-ds-text-primary">
                                            {template.project_path}
                                        </code>
                                    </div>
                                )}
                                {template.global_path && (
                                    <div className="space-y-1">
                                        <span className="text-xs text-ds-text-muted uppercase">
                                            Global
                                        </span>
                                        <code className="block border-2 border-ds-border bg-ds-bg-secondary/50 px-3 py-2 font-mono text-sm text-ds-text-primary">
                                            {template.global_path}
                                        </code>
                                    </div>
                                )}
                            </div>
                            {template.config_format && (
                                <p className="text-sm text-ds-text-muted">
                                    Format: {template.config_format}
                                    {template.file_extension &&
                                        ` (${template.file_extension})`}
                                </p>
                            )}
                        </div>
                    )}

                    {hasNpmInstall && (
                        <div className="space-y-3">
                            <div className="flex items-center gap-2 text-sm font-medium text-ds-text-secondary">
                                <Package className="h-4 w-4" />
                                <span>npm Package</span>
                            </div>
                            <p className="text-sm text-ds-text-secondary">
                                Add the plugin to your{' '}
                                <code className="bg-ds-bg-secondary px-1.5 py-0.5 font-mono text-xs">
                                    {template.npm_install?.config_file}
                                </code>{' '}
                                file:
                            </p>

                            <div className="border-2 border-ds-border bg-ds-bg-card">
                                <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                                    <span className="text-xs font-medium tracking-wider text-ds-text-muted uppercase">
                                        Config Example
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            copyToClipboard(
                                                template.npm_install?.example ??
                                                    '',
                                                setCopiedNpm,
                                            )
                                        }
                                        className="flex items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        {copiedNpm ? (
                                            <>
                                                <Check className="h-3.5 w-3.5 text-green-500" />
                                                <span className="text-green-500">
                                                    Copied
                                                </span>
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="h-3.5 w-3.5" />
                                                <span>Copy</span>
                                            </>
                                        )}
                                    </button>
                                </div>
                                <div className="overflow-x-auto">
                                    <pre className="p-4 text-sm leading-relaxed text-ds-text-primary">
                                        <code>
                                            {template.npm_install?.example}
                                        </code>
                                    </pre>
                                </div>
                            </div>
                        </div>
                    )}

                    {hasCliCommand && (
                        <div className="space-y-3">
                            <div className="flex items-center gap-2 text-sm font-medium text-ds-text-secondary">
                                <Terminal className="h-4 w-4" />
                                <span>CLI Command</span>
                            </div>

                            <div className="border-2 border-ds-border bg-ds-bg-card">
                                <div className="flex items-center justify-between border-b-2 border-ds-border px-4 py-2">
                                    <span className="text-xs font-medium tracking-wider text-ds-text-muted uppercase">
                                        Command
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            copyToClipboard(
                                                template.install_command!,
                                                setCopiedCommand,
                                            )
                                        }
                                        className="flex items-center gap-1 text-xs text-ds-text-muted transition-colors hover:text-ds-text-primary"
                                    >
                                        {copiedCommand ? (
                                            <>
                                                <Check className="h-3.5 w-3.5 text-green-500" />
                                                <span className="text-green-500">
                                                    Copied
                                                </span>
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="h-3.5 w-3.5" />
                                                <span>Copy</span>
                                            </>
                                        )}
                                    </button>
                                </div>
                                <div className="overflow-x-auto">
                                    <pre className="p-4 text-sm leading-relaxed text-ds-text-primary">
                                        <code>{template.install_command}</code>
                                    </pre>
                                </div>
                            </div>
                        </div>
                    )}

                    {template.plugin_structure && (
                        <div className="space-y-3">
                            <div className="flex items-center gap-2 text-sm font-medium text-ds-text-secondary">
                                <FileText className="h-4 w-4" />
                                <span>Plugin Structure</span>
                            </div>
                            <div className="grid gap-2 text-sm">
                                <div className="flex items-center gap-2">
                                    <span className="text-ds-text-muted">
                                        Manifest:
                                    </span>
                                    <code className="bg-ds-bg-secondary px-1.5 py-0.5 font-mono text-xs text-ds-text-primary">
                                        {template.plugin_structure.manifest}
                                    </code>
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="text-ds-text-muted">
                                        Commands:
                                    </span>
                                    <code className="bg-ds-bg-secondary px-1.5 py-0.5 font-mono text-xs text-ds-text-primary">
                                        {template.plugin_structure.commands_dir}
                                    </code>
                                </div>
                            </div>
                        </div>
                    )}

                    {template.test_command && (
                        <div className="flex items-center gap-2 text-sm text-ds-text-muted">
                            <span>Test locally:</span>
                            <code className="bg-ds-bg-secondary px-1.5 py-0.5 font-mono text-xs text-ds-text-primary">
                                {template.test_command}
                            </code>
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}
