<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __t('Підтвердження email') }}</h1>
        <p class="mt-2 text-sm text-gray-600">
            {{ __t('Перевірте пошту та клікніть на посилання для підтвердження.') }}
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            {{ __t('Нове посилання для підтвердження надіслано.') }}
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <button wire:click="sendVerification"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
            {{ __t('Надіслати ще раз') }}
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" data-test="logout-button"
                class="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                {{ __t('Вийти') }}
            </button>
        </form>
    </div>
</div>
