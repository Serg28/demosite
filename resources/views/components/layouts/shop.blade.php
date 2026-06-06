@props(['title' => config('app.name'), 'description' => '', 'seoPartial' => 'partials.seo', 'page' => null, 'count' => null])
{{-- $navCategories — надається NavCategoriesComposer --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include($seoPartial, ['seoTitle' => $title, 'seoDescription' => $description, 'page' => $page, 'count' => $count])
    @include('partials.analytics')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">

{{-- ══════════════════ MOBILE DRAWER ══════════════════ --}}
<div x-data="{ mobileOpen: false }"
     x-on:open-mobile-menu.window="mobileOpen = true"
     id="mobile-menu-root">

    {{-- Overlay --}}
    <div x-show="mobileOpen"
         x-transition.opacity
         class="fixed inset-0 bg-black/40 z-[119]"
         @click="mobileOpen = false"
         style="display:none"></div>

    {{-- Drawer --}}
    <aside x-show="mobileOpen"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed left-0 top-0 h-full w-80 max-w-full bg-white z-[120] flex flex-col shadow-2xl overflow-y-auto"
           style="display:none">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <a href="/" class="flex items-center gap-0.5">
                <span class="text-xl font-black text-ink">{{ config('app.name') }}</span>
            </a>
            <button @click="mobileOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-xl">×</button>
        </div>
        <nav class="p-4 flex-1">
            <p class="text-xs font-bold uppercase tracking-wider text-ink-muted mb-3">{{ __t('Каталог') }}</p>
            @foreach($navCategories as $cat)
                <a href="{{ $cat->getUrl() }}"
                   class="flex items-center gap-3 py-2.5 px-3 rounded-lg hover:bg-brand-light text-sm font-medium text-ink transition-colors">
                    {{ $cat->t('title') }}
                </a>
            @endforeach
            <hr class="my-4 border-gray-100">
            <a href="{{ route('compare.index') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg hover:bg-brand-light text-sm font-medium">{{ __t('Порівняння') }}</a>
            @auth
                <a href="{{ route('profile.favorites') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg hover:bg-brand-light text-sm font-medium">{{ __t('Список бажань') }}</a>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 py-2.5 px-3 rounded-lg hover:bg-brand-light text-sm font-medium">{{ __t('Профіль') }}</a>
            @else
                <button type="button"
                        data-js-modal data-component="auth.popup"
                        class="w-full text-left flex items-center gap-3 py-2.5 px-3 rounded-lg hover:bg-brand-light text-sm font-medium">
                    {{ __t('Увійти') }}
                </button>
            @endauth
        </nav>
    </aside>
</div>

{{-- ══════════════════ HEADER ══════════════════ --}}
<header class="bg-white shadow-sm sticky top-0 z-[90]"
        x-data="{ catalogOpen: false, hovCat: null }">

    {{-- Top bar --}}
    <div class="border-b border-gray-100 hidden md:block">
        <div class="max-w-[1264px] mx-auto px-4 sm:px-8 h-9 flex items-center justify-between text-xs text-ink-muted">
            <div class="flex items-center gap-5">
                <a href="#" class="hover:text-brand">{{ __t('Акції та знижки') }}</a>
                <a href="#" class="hover:text-brand">{{ __t('Оплата і доставка') }}</a>
                <a href="#" class="hover:text-brand">{{ __t('Контакти') }}</a>
                <a href="#" class="hover:text-brand">B2B</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="tel:+380671234567" class="font-medium hover:text-brand">📞 +38 (067) 123-45-67</a>
            </div>
        </div>
    </div>

    {{-- Main bar --}}
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 h-16 flex items-center gap-3 md:gap-4">

        {{-- Hamburger (mobile) --}}
        <button @click="$dispatch('open-mobile-menu')"
                class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 md:hidden flex-shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        {{-- Лого --}}
        <a href="/" class="flex items-center gap-0.5 flex-shrink-0 mr-1">
            <span class="text-xl sm:text-2xl font-black text-ink tracking-tight">{{ config('app.name', 'Shop') }}</span>
        </a>

        {{-- Каталог з mega-menu --}}
        <div class="relative hidden md:block flex-shrink-0"
             @mouseenter="catalogOpen = true"
             @mouseleave="catalogOpen = false">
            <button class="btn btn-p btn-sm gap-2 h-9">
                <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor">
                    <rect x="1" y="1" width="6" height="6" rx="1.5"/>
                    <rect x="9" y="1" width="6" height="6" rx="1.5"/>
                    <rect x="1" y="9" width="6" height="6" rx="1.5"/>
                    <rect x="9" y="9" width="6" height="6" rx="1.5"/>
                </svg>
                {{ __t('Каталог') }}
                <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                    <path d="M1 3l4 4 4-4"/>
                </svg>
            </button>

            {{-- Mega menu --}}
            <div x-show="catalogOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute left-0 top-full pt-1 z-[100]"
                 style="display:none">
                <div class="bg-white border border-gray-200 rounded-xl shadow-2xl overflow-hidden"
                     style="width:640px; min-height:320px">
                    <div class="flex" style="min-height:320px">
                        {{-- Category list --}}
                        <div class="w-56 flex-shrink-0 bg-gray-50 border-r border-gray-100 overflow-y-auto py-2">
                            @foreach($navCategories as $cat)
                                <a href="{{ $cat->getUrl() }}"
                                   @mouseenter="hovCat = {{ $cat->id }}"
                                   class="flex items-center justify-between px-4 py-2.5 text-sm text-left transition-colors"
                                   :class="hovCat === {{ $cat->id }} ? 'bg-white text-brand font-semibold' : 'hover:bg-white text-ink'">
                                    <span>{{ $cat->t('title') }}</span>
                                    @if($cat->children->isNotEmpty())
                                        <span class="text-ink-light">›</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        {{-- Sub-categories --}}
                        <div class="flex-1 p-6 min-w-0">
                            @foreach($navCategories as $cat)
                                <div x-show="hovCat === {{ $cat->id }}" style="display:none">
                                    <p class="text-xs text-ink-muted font-semibold uppercase tracking-wider mb-4">
                                        {{ $cat->t('title') }}
                                    </p>
                                    @if($cat->children->isNotEmpty())
                                        <div class="grid grid-cols-2 gap-x-3 gap-y-1">
                                            @foreach($cat->children as $sub)
                                                <a href="{{ $sub->getUrl() }}"
                                                   class="py-2 px-3 rounded-lg text-sm text-ink-muted hover:bg-brand-light hover:text-brand truncate transition-colors">
                                                    {{ $sub->t('title') }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <a href="{{ $cat->getUrl() }}" class="btn btn-p btn-sm">
                                            {{ __t('Всі товари') }}
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                            @if($navCategories->isNotEmpty())
                                <div x-show="!hovCat" class="text-ink-muted text-sm py-4">
                                    {{ __t('Оберіть категорію') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Пошук --}}
        <form action="{{ route('catalog.show', 'all') }}" method="GET" class="flex-1 relative min-w-0">
            <input type="text" name="q"
                   placeholder="{{ __t('Пошук товарів, брендів, артикулів...') }}"
                   class="field w-full"
                   style="padding:9px 40px 9px 14px">
            <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted hover:text-brand">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
            </button>
        </form>

        {{-- Іконки: профіль / вибране / порівняння / кошик --}}
        <div class="flex items-center gap-1 flex-shrink-0">
            {{-- Профіль --}}
            @auth
                <a href="{{ route('profile.index') }}"
                   class="hidden md:flex w-10 h-10 items-center justify-center rounded-lg hover:bg-gray-100 relative text-ink"
                   title="{{ __t('Профіль') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </a>
            @else
                <button type="button"
                        data-js-modal data-component="auth.popup"
                        class="hidden md:flex w-10 h-10 items-center justify-center rounded-lg hover:bg-gray-100 text-ink"
                        title="{{ __t('Увійти') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </button>
            @endauth

            {{-- Вибране --}}
            <a href="{{ route('profile.favorites') }}"
               class="hidden md:flex w-10 h-10 items-center justify-center rounded-lg hover:bg-gray-100 relative text-ink"
               title="{{ __t('Список бажань') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <livewire:favorite.count />
            </a>

            {{-- Порівняння --}}
            <a href="{{ route('compare.index') }}"
               class="hidden md:flex w-10 h-10 items-center justify-center rounded-lg hover:bg-gray-100 relative text-ink"
               title="{{ __t('Порівняння') }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M9 12h6"/>
                </svg>
                <livewire:compare.count />
            </a>

            {{-- Кошик --}}
            <livewire:cart.count />
        </div>
    </div>
</header>

{{-- ══════════════════ MAIN ══════════════════ --}}
<main>
    {{ $slot }}
</main>

{{-- ══════════════════ FOOTER ══════════════════ --}}
<footer class="bg-ink text-gray-400 mt-16">
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
            <div>
                <div class="flex items-center gap-0.5 mb-4">
                    <span class="text-xl font-black text-white">{{ config('app.name') }}</span>
                </div>
                <p class="text-xs leading-relaxed">{{ __t('Найбільший склад автотоварів та інструментів в Україні. Працюємо з 2012 року.') }}</p>
                <div class="flex gap-2 mt-4">
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand rounded flex items-center justify-center text-white text-xs font-bold">f</a>
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand rounded flex items-center justify-center text-white text-xs font-bold">in</a>
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand rounded flex items-center justify-center text-white text-xs font-bold">tg</a>
                    <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-brand rounded flex items-center justify-center text-white text-xs font-bold">yt</a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">{{ __t('Покупцям') }}</h4>
                <div class="space-y-2 text-sm">
                    <a href="#" class="block hover:text-brand">{{ __t('Оплата і доставка') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Гарантія') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Повернення') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Блог') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Статус замовлення') }}</a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">{{ __t('Компанія') }}</h4>
                <div class="space-y-2 text-sm">
                    <a href="#" class="block hover:text-brand">{{ __t('Про нас') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Контакти') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Всі бренди') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Вакансії') }}</a>
                    <a href="#" class="block hover:text-brand">{{ __t('Оптовий відділ') }}</a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4 text-sm uppercase tracking-wider">{{ __t('Контакти') }}</h4>
                <a href="tel:+380671234567" class="text-brand font-bold text-lg block mb-2">📞 +38 (067) 123-45-67</a>
                <p class="text-xs text-gray-500 mb-1">{{ __t('Пн–Пт 9:00–18:00, Сб 10:00–15:00') }}</p>
                <a href="mailto:info@shopdemo.ua" class="text-sm hover:text-brand block mb-4">info@shopdemo.ua</a>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 flex flex-wrap items-center justify-between gap-4 text-xs">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Powered by Linecore CMS.</p>
            <div class="flex items-center gap-3">
                <span class="bg-gray-800 px-2 py-1 rounded text-[10px] font-semibold">VISA</span>
                <span class="bg-gray-800 px-2 py-1 rounded text-[10px] font-semibold">MASTERCARD</span>
                <span class="bg-gray-800 px-2 py-1 rounded text-[10px] font-semibold">LiqPay</span>
                <span class="bg-gray-800 px-2 py-1 rounded text-[10px] font-semibold">MonoPay</span>
            </div>
        </div>
    </div>
</footer>

{{-- Системні компоненти --}}
<livewire:cart.drawer />
<livewire:components.modal-manager />
<x-toast />

@livewireScripts
@include('partials.analytics-body')
</body>
</html>
