<section class="w-full settings-page">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700">
            <p>Saat ini sistem hanya mendukung mode light untuk tampilan yang konsisten.</p>
        </div>
        
        <flux:radio.group variant="segmented" wire:model.live="appearance">
            <flux:radio value="light" icon="sun" checked>{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon" disabled class="opacity-50 cursor-not-allowed">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop" disabled class="opacity-50 cursor-not-allowed">{{ __('System') }}</flux:radio>
        </flux:radio.group>

        <script>
            // Force light mode
            document.addEventListener('DOMContentLoaded', function() {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('flux-ui-appearance', 'light');
            });
        </script>
    </x-settings.layout>
</section>
