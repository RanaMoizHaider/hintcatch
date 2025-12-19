import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { Form, Head, Link } from '@inertiajs/react';
import { Github, Gitlab } from 'lucide-react';

interface LoginProps {
    status?: string;
    canLogin: boolean;
    canResetPassword: boolean;
    canRegister: boolean;
    socialProviders?: {
        github: boolean;
        gitlab: boolean;
    };
}

export default function Login({
    status,
    canLogin,
    canResetPassword,
    canRegister,
    socialProviders,
}: LoginProps) {
    const hasSocialProviders =
        socialProviders?.github || socialProviders?.gitlab;

    return (
        <AuthLayout
            title="Log in to your account"
            description={
                canLogin
                    ? 'Sign in with your social account or email'
                    : 'Sign in with your social account'
            }
        >
            <Head title="Log in" />

            {hasSocialProviders && (
                <div
                    className={
                        canLogin
                            ? 'mb-6 flex flex-col gap-3'
                            : 'flex flex-col gap-3'
                    }
                >
                    {socialProviders?.github && (
                        <Button
                            variant="outline"
                            className="w-full border-ds-border bg-ds-bg-card text-ds-text-primary hover:bg-ds-bg-secondary"
                            asChild
                        >
                            <Link href="/auth/github">
                                <Github className="mr-2 h-4 w-4" />
                                Continue with GitHub
                            </Link>
                        </Button>
                    )}
                    {socialProviders?.gitlab && (
                        <Button
                            variant="outline"
                            className="w-full border-ds-border bg-ds-bg-card text-ds-text-primary hover:bg-ds-bg-secondary"
                            asChild
                        >
                            <Link href="/auth/gitlab">
                                <Gitlab className="mr-2 h-4 w-4" />
                                Continue with GitLab
                            </Link>
                        </Button>
                    )}

                    {canLogin && (
                        <div className="relative my-4">
                            <div className="absolute inset-0 flex items-center">
                                <span className="w-full border-t border-ds-border" />
                            </div>
                            <div className="relative flex justify-center text-xs uppercase">
                                <span className="bg-ds-bg-base px-2 text-ds-text-muted">
                                    Or continue with email
                                </span>
                            </div>
                        </div>
                    )}
                </div>
            )}

            {canLogin && (
                <Form
                    action="/login"
                    method="post"
                    className="flex flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-6">
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email address</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        required
                                        autoFocus={!hasSocialProviders}
                                        tabIndex={1}
                                        autoComplete="email"
                                        placeholder="email@example.com"
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="grid gap-2">
                                    <div className="flex items-center">
                                        <Label htmlFor="password">
                                            Password
                                        </Label>
                                        {canResetPassword && (
                                            <TextLink
                                                href="/forgot-password"
                                                className="ml-auto text-sm text-ds-text-muted hover:text-ds-text-primary"
                                                tabIndex={5}
                                            >
                                                Forgot password?
                                            </TextLink>
                                        )}
                                    </div>
                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        tabIndex={2}
                                        autoComplete="current-password"
                                        placeholder="Password"
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex items-center space-x-3">
                                    <Checkbox
                                        id="remember"
                                        name="remember"
                                        tabIndex={3}
                                    />
                                    <Label htmlFor="remember">
                                        Remember me
                                    </Label>
                                </div>

                                <Button
                                    type="submit"
                                    className="mt-4 w-full"
                                    tabIndex={4}
                                    disabled={processing}
                                    data-test="login-button"
                                >
                                    {processing && <Spinner />}
                                    Log in
                                </Button>
                            </div>

                            {canRegister && (
                                <div className="text-center text-sm text-ds-text-muted">
                                    Don't have an account?{' '}
                                    <TextLink href="/register" tabIndex={5}>
                                        Sign up
                                    </TextLink>
                                </div>
                            )}
                        </>
                    )}
                </Form>
            )}

            {status && (
                <div className="mt-4 text-center text-sm font-medium text-green-500">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
