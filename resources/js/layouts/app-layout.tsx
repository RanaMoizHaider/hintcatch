import AppHeaderLayout from '@/layouts/app/app-header-layout';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
}

export default ({ children }: AppLayoutProps) => (
    <AppHeaderLayout>{children}</AppHeaderLayout>
);
