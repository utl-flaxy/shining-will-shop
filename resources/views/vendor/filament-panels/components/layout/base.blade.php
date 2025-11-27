{{-- resources/views/vendor/filament-panels/components/layout/base.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        {{-- ✅ Alpine sidebar store 初期化 --}}
        <script>
            document.addEventListener('alpine:init', () => {
                try {
                    if (!Alpine.store('sidebar')) {
                        Alpine.store('sidebar', {
                            isOpen: true,
                            open() { this.isOpen = true },
                            close() { this.isOpen = false },
                            toggle() { this.isOpen = !this.isOpen },
                        });
                        console.log('✅ Alpine sidebar store initialized (base.blade)');
                    }
                } catch (e) {
                    console.warn('⚠️ sidebar store init failed', e);
                }
            });
        </script>

        {{-- Filament のスタイル --}}
        @filamentStyles

        {{-- Vite や追加CSS --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        @vite(['resources/css/filament-colorme.css'])

    </head>

    <body class="filament-body min-h-screen bg-gray-100 text-gray-900 antialiased">

        {{-- Filament Livewire 出力 --}}
        {{ $slot }}

        {{-- Filament JS --}}
        @filamentScripts

        {{-- Livewire JS --}}
        @livewireScripts

        {{-- 任意の追加スクリプト --}}
        @stack('scripts')

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                console.log('🚀 ページロード完了');
                if (typeof Livewire !== 'undefined') {
                    console.log('✅ Livewire 認識済み');
                } else {
                    console.error('❌ Livewire 未定義。Livewire Scriptsが正しく読み込まれていません。');
                }

                if (typeof Alpine !== 'undefined') {
                    console.log('✅ Alpine 認識済み');
                    if (Alpine.store('sidebar')) {
                        console.log('📦 sidebar store:', Alpine.store('sidebar'));
                    } else {
                        console.warn('⚠️ sidebar store 未定義');
                    }
                } else {
                    console.error('❌ Alpine 未定義。JSのロード順を確認してください。');
                }
            });
        </script>
    </body>
</html>
