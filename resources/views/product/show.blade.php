@php
    $onlinePrice = (int) round($product->getPrice() * 0.95);
@endphp

<x-layouts.shop
    :title="$product->getSeoTitle() . ' — ' . config('app.name')"
    :description="$product->getSeoDescription()"
    :page="$product"
>
    <div class="max-w-[1264px] mx-auto px-4 sm:px-8 py-6">

        {{-- Breadcrumbs --}}
        <x-breadcrumbs :items="$breadcrumbs" />

        {{-- Основна секція --}}
        <div class="flex gap-8 flex-wrap lg:flex-nowrap mt-5">

            {{-- Ліва колонка: галерея + розстрочка --}}
            <div class="w-full lg:w-[420px] flex-shrink-0">

                {{-- Галерея --}}
                <div class="card p-4 mb-4">
                    <livewire:product.gallery :product="$product" />
                </div>

                {{-- Розстрочка --}}
                @if($product->getPrice() > 0)
                    <div class="card p-5 mb-4" x-data="{ months: 6 }">
                        <p class="font-semibold text-sm mb-3">💳 {{ __t('Купити частинами') }}</p>
                        <div class="flex gap-2 mb-3">
                            @foreach([6, 10, 12] as $m)
                                <button
                                    @click="months = {{ $m }}"
                                    :class="months === {{ $m }} ? 'border-brand bg-brand-light text-brand' : 'border-gray-200 hover:border-brand'"
                                    class="flex-1 border rounded-lg py-2 text-xs font-bold transition"
                                >{{ $m }} {{ __t('міс') }}</button>
                            @endforeach
                        </div>
                        <p class="text-center text-sm">~ <strong x-text="Math.round({{ $product->getPrice() }} / months).toLocaleString('uk-UA') + ' ₴'"></strong> / {{ __t('місяць') }}</p>
                    </div>
                @endif

            </div>

            {{-- Права колонка: інформація --}}
            <div class="flex-1 min-w-0">

                {{-- Назва --}}
                <h1 class="text-2xl md:text-3xl font-bold leading-snug mb-3">{{ $product->getSeoH1() }}</h1>

                {{-- Статус + Код --}}
                <div class="flex items-center gap-4 text-sm mb-4">
                    @if($product->isActiveForOrder())
                        <span class="instock">● {{ __t('В наявності') }}</span>
                    @else
                        <span class="nostock">● {{ __t('Немає в наявності') }}</span>
                    @endif
                    @if($product->code)
                        <span class="text-ink-muted">{{ __t('Код') }}: <strong class="text-ink">{{ $product->code }}</strong></span>
                    @endif
                </div>

                {{-- Блок ціни --}}
                <div class="card p-6 mb-5">

                    {{-- Ціна --}}
                    <div class="flex items-end gap-3 mb-3">
                        <span class="text-4xl font-black">@money($product->getPrice()) ₴</span>
                        @if($product->hasDiscount())
                            <div class="mb-1 flex items-center gap-2">
                                <span class="text-lg text-ink-light line-through">@money($product->getPriceOld()) ₴</span>
                                <span class="badge bdg-sale">-{{ $product->getPriceDifferencePercentage() }}%</span>
                            </div>
                        @endif
                    </div>

                    {{-- Бонус онлайн-оплати --}}
                    <div class="bg-brand-light border border-blue-200 rounded-lg px-4 py-2.5 flex items-center gap-2 mb-5 text-sm">
                        <span>💳</span>
                        <strong>@money($onlinePrice) ₴</strong>
                        <span class="text-ink-muted">{{ __t('при онлайн-оплаті (-5%)') }}</span>
                    </div>

                    {{-- Кількість + Додати в кошик --}}
                    @if($product->isActiveForOrder())
                        <div x-data="{ qty: 1, max: {{ $product->getQuantity() }} }" class="flex items-center gap-3 mb-3">
                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden h-11">
                                <button
                                    @click="qty = Math.max(1, qty - 1)"
                                    class="w-10 text-xl font-bold text-ink-muted hover:text-brand hover:bg-brand-light h-full transition"
                                >−</button>
                                <span x-text="qty" class="w-10 text-center font-bold"></span>
                                <button
                                    @click="qty = Math.min(max, qty + 1)"
                                    class="w-10 text-xl font-bold text-ink-muted hover:text-brand hover:bg-brand-light h-full transition"
                                >+</button>
                            </div>
                            <x-buy-button
                                :product="$product"
                                alpine-count="qty"
                                class="btn-p flex-1 h-11 text-base"
                            />
                        </div>
                    @endif

                    {{-- Додаткові дії --}}
                    <div class="flex gap-2">
                        <button class="btn btn-o flex-1 text-sm h-11">📞 {{ __t('Купити в 1 клік') }}</button>

                        {{-- Вибране --}}
                        <button type="button"
                                data-js-like
                                data-id="{{ $product->id }}"
                                class="like-button w-11 h-11 border border-gray-200 rounded-lg flex items-center justify-center hover:border-red-300 hover:bg-red-50 text-gray-400 hover:text-red-400 transition"
                                title="{{ __t('Додати до вибраного') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

                        {{-- Порівняння --}}
                        <button type="button"
                                data-js-compare
                                data-id="{{ $product->id }}"
                                class="compare-button w-11 h-11 border border-gray-200 rounded-lg flex items-center justify-center hover:border-brand hover:bg-brand-light text-gray-400 hover:text-brand transition"
                                title="{{ __t('Додати до порівняння') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M9 12h6"/>
                            </svg>
                        </button>
                    </div>

                </div>

                {{-- Способи доставки --}}
                <div class="card p-5">
                    <p class="font-semibold text-sm mb-3">{{ __t('Способи доставки') }}</p>
                    <div class="space-y-0 text-sm divide-y divide-gray-50">
                        <div class="flex justify-between items-center py-2">
                            <span>📦 {{ __t('Нова Пошта') }}</span>
                            <span class="text-ink-muted">~ 95 ₴</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span>📮 {{ __t('Укрпошта') }}</span>
                            <span class="text-ink-muted">~ 75 ₴</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span>🏪 {{ __t('Самовивіз') }}</span>
                            <span class="instock font-semibold">{{ __t('Безкоштовно') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Вкладки --}}
        <div class="mt-10" x-data="{ tab: 'info' }">

            {{-- Навігація вкладок --}}
            <div class="flex gap-6 border-b mb-6 overflow-x-auto">
                @foreach(['info' => __t('Опис'), 'specs' => __t('Характеристики'), 'reviews' => __t('Відгуки')] as $tabKey => $tabLabel)
                    <button
                        @click="tab = '{{ $tabKey }}'"
                        class="pb-3 text-sm font-semibold border-b-2 flex-shrink-0 transition"
                        :class="tab === '{{ $tabKey }}' ? 'border-brand text-brand' : 'border-transparent text-ink-muted hover:text-ink'"
                    >{{ $tabLabel }}</button>
                @endforeach
            </div>

            {{-- Опис --}}
            <div x-show="tab === 'info'">
                @if($product->t('description'))
                    <div class="prose prose-sm max-w-2xl text-ink-muted leading-relaxed mb-8">
                        {!! $product->t('description') !!}
                    </div>
                @elseif($product->t('short_description'))
                    <div class="prose prose-sm max-w-2xl text-ink-muted leading-relaxed mb-8">
                        {!! nl2br(e($product->t('short_description'))) !!}
                    </div>
                @endif

                <livewire:product.similar :product="$product" />
            </div>

            {{-- Характеристики --}}
            <div x-show="tab === 'specs'" class="max-w-xl">
                @php
                    $specs = $product->characteristics
                        ->groupBy(fn($opt) => optional($opt->characteristic)->id)
                        ->map(fn($opts) => [
                            'name'   => optional($opts->first()->characteristic)->t('title'),
                            'values' => $opts->pluck('title')->map(fn($t) => is_array($t) ? ($t[app()->getLocale()] ?? reset($t)) : $t)->implode(', '),
                        ])
                        ->values();
                @endphp

                <div class="card divide-y">
                    @if($product->code)
                        <div class="flex px-5 py-3 text-sm">
                            <span class="w-44 text-ink-muted flex-shrink-0">{{ __t('Артикул') }}</span>
                            <span class="font-medium">{{ $product->code }}</span>
                        </div>
                    @endif
                    @if($product->category)
                        <div class="flex px-5 py-3 text-sm">
                            <span class="w-44 text-ink-muted flex-shrink-0">{{ __t('Категорія') }}</span>
                            <span class="font-medium">{{ $product->category->t('title') }}</span>
                        </div>
                    @endif
                    @foreach($specs as $spec)
                        @if($spec['name'] && $spec['values'])
                            <div class="flex px-5 py-3 text-sm">
                                <span class="w-44 text-ink-muted flex-shrink-0">{{ $spec['name'] }}</span>
                                <span class="font-medium">{{ $spec['values'] }}</span>
                            </div>
                        @endif
                    @endforeach
                    <div class="flex px-5 py-3 text-sm">
                        <span class="w-44 text-ink-muted flex-shrink-0">{{ __t('Наявність') }}</span>
                        <span class="font-medium {{ $product->isActiveForOrder() ? 'instock' : 'nostock' }}">
                            {{ $product->isActiveForOrder() ? __t('В наявності') : __t('Немає') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Відгуки --}}
            <div x-show="tab === 'reviews'" class="max-w-xl">
                <p class="text-sm text-ink-muted">{{ __t('Відгуки поки відсутні') }}</p>
            </div>

        </div>

    </div>
</x-layouts.shop>
