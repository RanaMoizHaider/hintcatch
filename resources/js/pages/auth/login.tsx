import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/auth-layout';
import { Head } from '@inertiajs/react';
import { Github, Gitlab } from 'lucide-react';

interface LoginProps {
    status?: string;
    socialProviders?: {
        github: boolean;
        gitlab: boolean;
    };
}

export default function Login({ status, socialProviders }: LoginProps) {
    return (
        <AuthLayout
            title="Sign in to HintCatch"
            description="Continue with your social account"
        >
            <Head title="Sign in" />

            <div className="flex flex-col gap-3">
                {socialProviders?.github && (
                    <Button
                        variant="outline"
                        className="w-full border-ds-border bg-ds-bg-card text-ds-text-primary hover:bg-ds-bg-secondary"
                        asChild
                    >
                        <a href="/auth/github">
                            <Github className="mr-2 h-4 w-4" />
                            Continue with GitHub
                        </a>
                    </Button>
                )}
                {socialProviders?.gitlab && (
                    <Button
                        variant="outline"
                        className="w-full border-ds-border bg-ds-bg-card text-ds-text-primary hover:bg-ds-bg-secondary"
                        asChild
                    >
                        <a href="/auth/gitlab">
                            <Gitlab className="mr-2 h-4 w-4" />
                            Continue with GitLab
                        </a>
                    </Button>
                )}
            </div>

            {status && (
                <div className="mt-4 text-center text-sm font-medium text-green-500">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
