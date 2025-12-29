import { Head } from '@inertiajs/react';

interface SeoHeadProps {
    title: string;
    description?: string;
    ogImage?: string;
    ogType?: string;
    canonicalUrl?: string;
    ogUrl?: string;
    noIndex?: boolean;
    children?: React.ReactNode;
}

export function SeoHead({
    title,
    description,
    ogImage,
    ogType = 'website',
    canonicalUrl,
    ogUrl,
    noIndex,
    children,
}: SeoHeadProps) {
    return (
        <Head title={title}>
            {description && (
                <meta
                    head-key="description"
                    name="description"
                    content={description}
                />
            )}

            <meta head-key="og:title" property="og:title" content={title} />
            {description && (
                <meta
                    head-key="og:description"
                    property="og:description"
                    content={description}
                />
            )}
            <meta head-key="og:type" property="og:type" content={ogType} />
            {ogUrl && (
                <meta head-key="og:url" property="og:url" content={ogUrl} />
            )}
            {ogImage && (
                <meta
                    head-key="og:image"
                    property="og:image"
                    content={ogImage}
                />
            )}

            <meta
                head-key="twitter:card"
                name="twitter:card"
                content="summary_large_image"
            />
            <meta
                head-key="twitter:title"
                name="twitter:title"
                content={title}
            />
            {description && (
                <meta
                    head-key="twitter:description"
                    name="twitter:description"
                    content={description}
                />
            )}
            {ogImage && (
                <meta
                    head-key="twitter:image"
                    name="twitter:image"
                    content={ogImage}
                />
            )}

            {canonicalUrl && (
                <link
                    head-key="canonical"
                    rel="canonical"
                    href={canonicalUrl}
                />
            )}

            {noIndex && (
                <meta
                    head-key="robots"
                    name="robots"
                    content="noindex, nofollow"
                />
            )}

            {children}
        </Head>
    );
}
