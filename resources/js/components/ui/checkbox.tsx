'use client';

import * as React from 'react';
import * as CheckboxPrimitive from '@radix-ui/react-checkbox';
import { CheckIcon } from 'lucide-react';

import { cn } from '@/lib/utils';

function Checkbox({
    className,
    ...props
}: React.ComponentProps<typeof CheckboxPrimitive.Root>) {
    return (
        <CheckboxPrimitive.Root
            data-slot="checkbox"
            className={cn(
                'peer size-4 shrink-0 rounded-none border border-ds-border bg-ds-bg-elevated shadow-xs transition-shadow outline-none disabled:cursor-not-allowed disabled:opacity-50',
                'data-[state=checked]:bg-white data-[state=checked]:text-black data-[state=checked]:border-white',
                'focus-visible:border-ds-border-hover focus-visible:ring-0',
                'aria-invalid:border-destructive',
                className,
            )}
            {...props}
        >
            <CheckboxPrimitive.Indicator
                data-slot="checkbox-indicator"
                className="flex items-center justify-center text-current transition-none"
            >
                <CheckIcon className="size-3.5" />
            </CheckboxPrimitive.Indicator>
        </CheckboxPrimitive.Root>
    );
}

export { Checkbox };
