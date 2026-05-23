<div x-data="{ showRecovery: false }" class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">
            <span x-show="!showRecovery">{{ __t('Код автентифікації') }}</span>
            <span x-show="showRecovery">{{ __t('Код відновлення') }}</span>
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            <span x-show="!showRecovery">{{ __t('Введіть код з вашого застосунку-автентифікатора') }}</span>
            <span x-show="showRecovery">{{ __t('Введіть один з резервних кодів') }}</span>
        </p>
    </div>

    @if ($errors->any())
        <div class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.login.store') }}" class="flex flex-col gap-4">
        @csrf

        <div x-show="!showRecovery">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __t('Код') }}</label>
            <input type="text" name="code" autocomplete="one-time-code" inputmode="numeric" maxlength="6"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('code')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-show="showRecovery">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __t('Резервний код') }}</label>
            <input type="text" name="recovery_code" autocomplete="one-time-code"
                :required="showRecovery"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('recovery_code')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
            {{ __t('Продовжити') }}
        </button>
    </form>

    <div class="text-sm text-center text-gray-600">
        <span x-show="!showRecovery">
            {{ __t('або') }}
            <button @click="showRecovery = true" type="button" class="text-blue-600 hover:underline">
                {{ __t('увійти через резервний код') }}
            </button>
        </span>
        <span x-show="showRecovery">
            {{ __t('або') }}
            <button @click="showRecovery = false" type="button" class="text-blue-600 hover:underline">
                {{ __t('увійти через код автентифікатора') }}
            </button>
        </span>
    </div>
</div>
