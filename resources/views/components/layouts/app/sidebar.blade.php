<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-gray-50">
    <flux:sidebar sticky stashable
        class="border-r border-gray-200 bg-[#0F1945] overflow-x-hidden max-w-[260px] shadow-sm">
        <flux:sidebar.toggle class="lg:hidden text-white" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="flex items-center p-3 mb-2" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="text-gray-400 font-medium px-3 text-sm">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Data Management')" class="text-gray-400 font-medium px-3 text-sm">
                <flux:navlist.item icon="circle-stack" :href="route('student.dataset')"
                    :current="request()->routeIs('student.dataset')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('student.dataset') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('Dataset Siswa') }}</flux:navlist.item>
                <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('users.*') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('Manajemen Pengguna') }}</flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Analisis Clustering')" class="text-gray-400 font-medium px-3 text-sm">
                <flux:navlist.item icon="chart-bar" :href="route('clustering.elbow')"
                    :current="request()->routeIs('clustering.elbow')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('clustering.elbow') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('1. Elbow Method') }}</flux:navlist.item>
                <flux:navlist.item icon="adjustments-horizontal" :href="route('clustering.setup')"
                    :current="request()->routeIs('clustering.setup')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('clustering.setup') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('2. Setup Clustering') }}</flux:navlist.item>
                <flux:navlist.item icon="circle-stack" :href="route('clustering.kmeans')"
                    :current="request()->routeIs('clustering.kmeans')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('clustering.kmeans') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('3. Proses K-Means') }}</flux:navlist.item>
                <flux:navlist.item icon="presentation-chart-bar" :href="route('clustering.result')"
                    :current="request()->routeIs('clustering.result')"
                    class="text-white hover:bg-[#1A237E] rounded-lg transition-colors duration-200 {{ request()->routeIs('clustering.result') ? 'bg-[#1A237E]' : '' }}">
                    {{ __('4. Hasil Clustering') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:navlist.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                    class="text-white hover:bg-red-900/50 w-full flex items-center rounded-lg transition-colors duration-200">
                    {{ __('Keluar') }}
                </flux:navlist.item>
            </form>
        </flux:navlist>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden bg-[#0F1945] border-b border-gray-700">
        <flux:sidebar.toggle class="lg:hidden text-white" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" class="text-white" />

            <flux:menu class="bg-[#0F1945] border border-gray-700">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-[#1A237E] text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold text-white">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs text-gray-400">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator class="border-gray-700" />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full text-white hover:bg-[#1A237E]">
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