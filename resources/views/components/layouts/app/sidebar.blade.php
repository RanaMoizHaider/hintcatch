<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('home') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                @if(auth()->user()->is_admin)
                    <!-- Admin Navigation -->
                    <flux:navlist.group :heading="__('Admin Dashboard')" class="grid">
                        <flux:navlist.item icon="chart-bar-square" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group :heading="__('Content Management')" class="grid">
                        <flux:navlist.item icon="folder" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>{{ __('Categories') }}</flux:navlist.item>
                        <flux:navlist.item icon="building-office" :href="route('admin.providers.index')" :current="request()->routeIs('admin.providers.*')" wire:navigate>{{ __('Providers') }}</flux:navlist.item>
                        <flux:navlist.item icon="cpu-chip" :href="route('admin.ai-models.index')" :current="request()->routeIs('admin.ai-models.*')" wire:navigate>{{ __('AI Models') }}</flux:navlist.item>
                        <flux:navlist.item icon="device-tablet" :href="route('admin.platforms.index')" :current="request()->routeIs('admin.platforms.*')" wire:navigate>{{ __('Platforms') }}</flux:navlist.item>
                        <flux:navlist.item icon="chat-bubble-left-right" :href="route('admin.prompts.index')" :current="request()->routeIs('admin.prompts.*')" wire:navigate>{{ __('Prompts') }}</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group :heading="__('Public Site')" class="grid">
                        <flux:navlist.item icon="globe-alt" :href="route('home')" wire:navigate>{{ __('View Site') }}</flux:navlist.item>
                    </flux:navlist.group>
                @else
                    <!-- User Navigation -->
                    <flux:navlist.group :heading="__('Dashboard')" class="grid">
                        <flux:navlist.item icon="home" :href="route('user.dashboard')" :current="request()->routeIs('user.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group :heading="__('My Content')" class="grid">
                        <flux:navlist.item icon="chat-bubble-left-right" :href="route('user.prompts.index')" :current="request()->routeIs('user.prompts.*')" wire:navigate>{{ __('My Prompts') }}</flux:navlist.item>
                        <flux:navlist.item icon="plus" :href="route('user.prompts.create')" :current="request()->routeIs('user.prompts.create')" wire:navigate>{{ __('Create Prompt') }}</flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.group :heading="__('Explore')" class="grid">
                        <flux:navlist.item icon="globe-alt" :href="route('home')" wire:navigate>{{ __('Browse Prompts') }}</flux:navlist.item>
                        <flux:navlist.item icon="magnifying-glass" :href="route('explore')" wire:navigate>{{ __('Explore') }}</flux:navlist.item>
                        <flux:navlist.item icon="folder" :href="route('categories.index')" wire:navigate>{{ __('Categories') }}</flux:navlist.item>
                        <flux:navlist.item icon="cpu-chip" :href="route('models.index')" wire:navigate>{{ __('AI Models') }}</flux:navlist.item>
                        <flux:navlist.item icon="device-tablet" :href="route('platforms.index')" wire:navigate>{{ __('Platforms') }}</flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/RanaMoizHaider/hintcatch" target="_blank">
                {{ __('GitHub') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :avatar="auth()->user()->gravatar"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <img src="{{ auth()->user()->gravatar }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover rounded-lg" />
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :avatar="auth()->user()->gravatar"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <img src="{{ auth()->user()->gravatar }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover rounded-lg" />
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
