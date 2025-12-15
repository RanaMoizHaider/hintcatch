import * as React from 'react';

import { cn } from '@/lib/utils';

function Input({ className, type, ...props }: React.ComponentProps<'input'>) {
    return (
        <input
            type={type}
            data-slot="input"
            className={cn(
                'file:text-foreground placeholder:text-ds-text-subtle selection:bg-primary selection:text-primary-foreground flex h-10 w-full min-w-0 rounded-none border border-ds-border bg-ds-bg-elevated px-3 py-2 text-base text-ds-text-primary outline-none transition-[color,box-shadow] file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50',
                'focus-visible:border-ds-border-hover focus-visible:ring-0',
                'aria-invalid:border-destructive',
                className,
            )}
            {...props}
        />
    );
}

export { Input };
