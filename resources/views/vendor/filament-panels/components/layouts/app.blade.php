<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{ \Filament\Support\Facades\FilamentView::renderHook('head.start') }}

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? config('app.name', 'Shining Will Shop') }}</title>

    @filamentStyles
    @vite('resources/css/app.css')
    {{ \Filament\Support\Facades\FilamentView::renderHook('head.end') }}
</head>

<body class="filament-body font-sans antialiased bg-gray-50 text-gray-900">
    {{ \Filament\Support\Facades\FilamentView::renderHook('body.start') }}

    {{ $slot }}

    {{ \Filament\Support\Facades\FilamentView::renderHook('body.end') }}

    @filamentScripts
    @livewireScripts
</body>
</html>
