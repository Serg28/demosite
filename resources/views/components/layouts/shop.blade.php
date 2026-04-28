@props(['title' => config('app.name'), 'description' => '', 'seoPartial' => 'partials.seo', 'page' => null, 'count' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include($seoPartial, ['seoTitle' => $title, 'seoDescription' => $description, 'page' => $page, 'count' => $count])
        @include('partials.analytics')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="/" class="text-xl font-bold text-blue-600">
                        {{ config('app.name', 'Demo Shop') }}
                    </a>
                    <nav class="hidden md:flex items-center gap-6 text-sm">
                        <a href="/" class="hover:text-blue-600 transition">{{ __t('Головна') }}</a>
                        <a href="/catalog" class="hover:text-blue-600 transition">{{ __t('Каталог') }}</a>
                        <a href="/brands" class="hover:text-blue-600 transition">{{ __t('Бренди') }}</a>
                    </nav>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-blue-600">
                                {{ auth()->user()->name }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600">
                                {{ __t('Увійти') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-16 py-8">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
        </footer>

        @livewireScripts
        @include('partials.analytics-body')
        <notifications-popup styles-path="/css/notifications.css"></notifications-popup>
    </body>
</html>
