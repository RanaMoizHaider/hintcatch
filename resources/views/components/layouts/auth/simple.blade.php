<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <!-- Dark Mode Toggle -->
            <div class="absolute top-4 right-4">
                <flux:button 
                    x-data 
                    @click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                    variant="ghost"
                    square
                    class="h-10 w-10"
                    title="Toggle dark mode"
                >
                    <flux:icon.moon x-show="$flux.appearance === 'light'" class="size-5" />
                    <flux:icon.sun x-show="$flux.appearance === 'dark'" class="size-5" />
                </flux:button>
            </div>
            
            <div class="flex w-full max-w-sm flex-col gap-2">
                <flux:link href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </flux:link>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
