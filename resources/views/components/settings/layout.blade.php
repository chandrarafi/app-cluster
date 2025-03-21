<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist class="bg-white">
            <flux:navlist.item :href="route('settings.profile')" wire:navigate class="text-gray-700 hover:text-blue-600 hover:bg-gray-50">{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.password')" wire:navigate class="text-gray-700 hover:text-blue-600 hover:bg-gray-50">{{ __('Password') }}</flux:navlist.item>
            <flux:navlist.item :href="route('settings.appearance')" wire:navigate class="text-gray-700 hover:text-blue-600 hover:bg-gray-50">{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6 bg-white">
        <flux:heading class="text-gray-900">{{ $heading ?? '' }}</flux:heading>
        <flux:subheading class="text-gray-600">{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
    // Force light mode on settings page
    document.addEventListener('DOMContentLoaded', function() {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
        document.body.classList.add('light-mode');
        localStorage.setItem('flux-ui-appearance', 'light');
    });
</script>
