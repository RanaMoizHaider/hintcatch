import * as React from 'react';
import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';

import { cn } from '@/lib/utils';

const buttonVariants = cva(
    "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-none text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:ring-0",
    {
        variants: {
            variant: {
                default:
                    'bg-white text-black shadow-xs hover:bg-neutral-200',
                destructive:
                    'bg-destructive text-white shadow-xs hover:bg-destructive/90',
                outline:
                    'border border-ds-border bg-transparent text-ds-text-primary shadow-xs hover:bg-ds-bg-secondary hover:border-ds-border-hover',
                secondary:
                    'bg-ds-bg-secondary text-ds-text-primary shadow-xs hover:bg-ds-bg-tertiary',
                ghost: 'hover:bg-ds-bg-secondary hover:text-ds-text-primary text-ds-text-muted',
                link: 'text-ds-text-primary underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-10 px-4 py-2 has-[>svg]:px-3',
                sm: 'h-8 gap-1.5 px-3 has-[>svg]:px-2.5',
                lg: 'h-11 px-6 has-[>svg]:px-4',
                icon: 'size-10',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

function Button({
    className,
    variant,
    size,
    asChild = false,
    ...props
}: React.ComponentProps<'button'> &
    VariantProps<typeof buttonVariants> & {
        asChild?: boolean;
    }) {
    const Comp = asChild ? Slot : 'button';

    return (
        <Comp
            data-slot="button"
            className={cn(buttonVariants({ variant, size, className }))}
            {...props}
        />
    );
}

export { Button, buttonVariants };
