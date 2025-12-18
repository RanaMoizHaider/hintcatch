import { show as showConfigType } from '@/actions/App/Http/Controllers/ConfigTypeController';
import { SiteFooter } from '@/components/layout/site-footer';
import { SiteHeader } from '@/components/layout/site-header';
import { SeoHead } from '@/components/seo-head';
import type { ConfigTypeIndexPageProps } from '@/types/models';
import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';

export default function ConfigTypesIndex({
    configTypes,
}: ConfigTypeIndexPageProps) {
    return (
        <>
            <SeoHead
                title="Config Types"
                description="Browse configurations by type - rules, prompts, MCP settings, and more."
            />
            <div className="flex min-h-screen flex-col bg-ds-bg-base">
                <SiteHeader />

                <main className="flex-1">
                    <section className="border-b-2 border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <h1 className="text-2xl font-medium text-ds-text-primary uppercase md:text-3xl">
                                Config Types
                            </h1>
                            <p className="mt-2 text-ds-text-secondary">
                                Browse configurations by type - rules, prompts,
                                MCP settings, and more
                            </p>
                        </div>
                    </section>

                    <section className="border-ds-border">
                        <div className="mx-auto max-w-[1200px] px-4 py-8 md:px-6 md:py-12">
                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                                {configTypes.map((configType) => (
                                    <Link
                                        key={configType.id}
                                        href={showConfigType(configType.slug)}
                                        className="group flex flex-col border-2 border-ds-border bg-ds-bg-card p-6 transition-colors hover:border-ds-text-muted"
                                    >
                                        <div className="flex items-center justify-between">
                                            <h3 className="text-lg font-medium text-ds-text-primary uppercase group-hover:text-ds-text-secondary">
                                                {configType.name}
                                            </h3>
                                            <ArrowRight className="h-4 w-4 text-ds-text-muted transition-transform group-hover:translate-x-1" />
                                        </div>
                                        {configType.description && (
                                            <p className="mt-2 text-sm text-ds-text-secondary">
                                                {configType.description}
                                            </p>
                                        )}
                                        <div className="mt-4 text-xs text-ds-text-muted">
                                            {configType.configs_count ?? 0}{' '}
                                            configs
                                        </div>
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </section>
                </main>

                <SiteFooter />
            </div>
        </>
    );
}
