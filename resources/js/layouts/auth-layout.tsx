import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';

interface AuthLayoutProps {
    children: React.ReactNode;
    title?: string;
    description?: string;
}

export default function AuthLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-ds-bg-base p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href="/"
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="mb-1 flex h-10 w-10 items-center justify-center rounded-md">
                                <AppLogoIcon className="size-10" />
                            </div>
                            <span className="sr-only">HintCatch</span>
                        </Link>

                        {title && (
                            <h1 className="text-xl font-bold text-ds-text-primary">
                                {title}
                            </h1>
                        )}
                        {description && (
                            <p className="text-center text-sm text-ds-text-muted">
                                {description}
                            </p>
                        )}
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
