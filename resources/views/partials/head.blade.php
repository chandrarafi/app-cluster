<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Force light mode -->
<script>
    // Set light mode
    document.documentElement.classList.remove('dark');
    localStorage.setItem('flux-ui-appearance', 'light');
</script>
<style>
    .dark {
        color-scheme: light !important;
    }
    [data-theme="dark"] {
        color-scheme: light !important;
    }
</style>
