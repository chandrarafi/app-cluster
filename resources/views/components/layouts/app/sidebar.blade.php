<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white light-mode">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-[#0F1945] overflow-x-hidden max-w-[260px]">
            <flux:sidebar.toggle class="lg:hidden text-white" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="flex items-center p-3 mb-2 bg-[#0F1945] text-white" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline" class="text-white">
                <flux:navlist.group :heading="__('Platform')" class="grid text-gray-200">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" class="text-white hover:bg-blue-700">{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <flux:navlist.group :heading="__('Data Management')" class="grid text-gray-200">
                    <flux:navlist.item icon="circle-stack" :href="route('student.dataset')" :current="request()->routeIs('student.dataset')" class="text-white hover:bg-blue-700">{{ __('Dataset Siswa') }}</flux:navlist.item>
                </flux:navlist.group>
                
                <flux:navlist.group :heading="__('Analisis Clustering')" class="grid text-gray-200">
                    <flux:navlist.item icon="chart-bar" :href="route('clustering.elbow')" :current="request()->routeIs('clustering.elbow')" class="text-white hover:bg-blue-700">{{ __('1. Elbow Method') }}</flux:navlist.item>
                    <flux:navlist.item icon="adjustments-horizontal" :href="route('clustering.setup')" :current="request()->routeIs('clustering.setup')" class="text-white hover:bg-blue-700">{{ __('2. Setup Clustering') }}</flux:navlist.item>
                    <flux:navlist.item icon="circle-stack" :href="route('clustering.kmeans')" :current="request()->routeIs('clustering.kmeans')" class="text-white hover:bg-blue-700">{{ __('3. Proses K-Means') }}</flux:navlist.item>
                    <flux:navlist.item icon="presentation-chart-bar" :href="route('clustering.result')" :current="request()->routeIs('clustering.result')" class="text-white hover:bg-blue-700">{{ __('4. Hasil Clustering') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline" class="text-white">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:navlist.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="text-white hover:bg-red-700 w-full flex items-center">
                        {{ __('Keluar') }}
                    </flux:navlist.item>
                </form>
            </flux:navlist>

            {{-- <flux:navlist variant="outline" class="text-white">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="text-white hover:bg-blue-700">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank" class="text-white hover:bg-blue-700">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

            <!-- Desktop User Menu -->
            {{-- <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                    class="text-white"
                />

                <flux:menu class="w-[220px] bg-[#0F1945]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-[#1A2563] text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold text-white">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-gray-300">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator class="border-gray-600" />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate class="text-white hover:bg-blue-700">{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator class="border-gray-600" />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full text-white hover:bg-blue-700">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown> --}}
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-white border-b border-zinc-200">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="light-mode"
                />

                <flux:menu class="light-mode">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-gray-600">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate class="text-gray-700">{{ __('Settings') }}</flux:menu.item>
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
