import { Icons } from '@/components/ui/icons';
import { dashboard, login } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome" />
            <div className="flex min-h-screen flex-col bg-ds-bg-base p-2 md:p-4">
                {/* Main Container with Border */}
                <div className="flex flex-1 flex-col border-2 border-ds-border">
                    {/* Hero Section */}
                    <section className="p-6 md:p-12">
                        <div className="mb-4 flex items-center gap-3">
                            <Icons.logo className="h-16 w-16 md:h-24 md:w-24" />
                        </div>
                        <h1 className="text-2xl leading-tight font-normal tracking-tight text-ds-text-primary uppercase md:text-4xl">
                            Catch hints.
                            <br />
                            Ship faster.
                        </h1>
                    </section>

                    {/* CTA Section */}
                    <section className="flex flex-col border-t-2 border-ds-border md:flex-row">
                        <div className="flex items-center justify-center border-b-2 border-ds-border px-6 py-4 md:border-r-2 md:border-b-0 md:px-12">
                            {auth.user ? (
                                <Link
                                    href={dashboard()}
                                    className="text-base text-ds-text-primary uppercase transition-colors hover:text-ds-text-secondary"
                                >
                                    Go to Dashboard
                                </Link>
                            ) : (
                                <Link
                                    href={login()}
                                    className="text-base text-ds-text-primary uppercase transition-colors hover:text-ds-text-secondary"
                                >
                                    Get Started
                                </Link>
                            )}
                        </div>
                        <div className="flex flex-1 items-center justify-center px-4 py-4 md:justify-start md:px-6">
                            <code className="text-center text-sm text-ds-text-muted md:text-lg">
                                <span>Built with </span>
                                <span className="font-medium text-ds-text-primary">
                                    Laravel
                                </span>
                                <span> + </span>
                                <span className="font-medium text-ds-text-primary">
                                    React
                                </span>
                                <span> + </span>
                                <span className="font-medium text-ds-text-primary">
                                    Inertia.js
                                </span>
                            </code>
                        </div>
                    </section>

                    {/* Features Section */}
                    <section className="border-t-2 border-ds-border p-6 md:p-12">
                        <ul className="space-y-3 pl-4 text-sm text-ds-text-secondary md:text-base">
                            <li>
                                <span className="font-medium text-ds-text-primary uppercase">
                                    Lightning Fast
                                </span>
                                : SPA-like experiences with server-side routing
                                via Inertia.js.
                            </li>
                            <li>
                                <span className="font-medium text-ds-text-primary uppercase">
                                    Type Safe
                                </span>
                                : Full TypeScript support with Wayfinder for
                                route generation.
                            </li>
                            <li>
                                <span className="font-medium text-ds-text-primary uppercase">
                                    Modern Stack
                                </span>
                                : React 19, Tailwind CSS v4, and Laravel 12.
                            </li>
                            <li>
                                <span className="font-medium text-ds-text-primary uppercase">
                                    Auth Ready
                                </span>
                                : GitHub and GitLab OAuth authentication out of
                                the box.
                            </li>
                            <li>
                                <span className="font-medium text-ds-text-primary uppercase">
                                    Beautiful UI
                                </span>
                                : Built with shadcn/ui components for endless
                                flexibility.
                            </li>
                        </ul>
                    </section>

                    {/* Auth Links Section */}
                    {!auth.user && (
                        <section className="grid border-t-2 border-ds-border">
                            <div className="px-4 py-4 text-center md:px-8">
                                <h3 className="mb-2 text-xs text-ds-text-muted uppercase md:text-sm">
                                    Existing User
                                </h3>
                                <Link
                                    href={login()}
                                    className="text-sm font-medium text-ds-text-primary transition-colors hover:text-ds-text-secondary md:text-base"
                                >
                                    Sign In
                                </Link>
                            </div>
                        </section>
                    )}

                    {/* Footer */}
                    <footer className="mt-auto flex flex-wrap border-t-2 border-ds-border">
                        <div className="flex flex-1 items-center justify-center border-b-2 border-ds-border px-4 py-3 md:flex-initial md:border-r-2 md:border-b-0">
                            <a
                                href="https://github.com"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-sm text-ds-text-muted uppercase transition-colors hover:text-ds-text-primary"
                            >
                                GitHub
                            </a>
                        </div>
                        <div className="flex flex-1 items-center justify-center border-b-2 border-ds-border px-4 py-3 md:flex-initial md:border-r-2 md:border-b-0">
                            <a
                                href="https://laravel.com/docs"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-sm text-ds-text-muted uppercase transition-colors hover:text-ds-text-primary"
                            >
                                Docs
                            </a>
                        </div>
                        <div className="flex w-full flex-1 items-center justify-center px-4 py-3 md:w-auto">
                            <span className="text-sm text-ds-text-muted uppercase">
                                &copy;{new Date().getFullYear()} HintCatch
                            </span>
                        </div>
                    </footer>
                </div>
            </div>
        </>
    );
}
