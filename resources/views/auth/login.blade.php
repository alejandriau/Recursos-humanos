<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-16" />
            </div>
        </x-slot>

        <x-validation-errors class="mb-6" />

        @session('status')
            <div class="mb-6 p-3 text-sm text-blue-600 bg-blue-50 rounded-lg text-center">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <x-label for="email" value="{{ __('Email') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="email"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         
                         name="email"
                         :value="old('email')"
                         required
                         autofocus
                         autocomplete="username"
                         />
            </div>

            <div class="space-y-2">
                <x-label for="password" value="{{ __('Contraseña') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="password"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         type="password"
                         name="password"
                         required
                         autocomplete="current-password"
                          />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center space-x-2 cursor-pointer">
                    <x-checkbox id="remember_me"
                                name="remember"
                                class="border-gray-300 text-blue-800 focus:ring-blue-800/30" />
                    <span class="text-sm text-gray-600">{{ __('Recordarme') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-800 hover:text-blue-900 transition-colors"
                       href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <div class="pt-4">
                <x-button class="w-full justify-center bg-blue-800 hover:bg-blue-900 focus:bg-blue-900 active:bg-blue-950 text-white">
                    {{ __('Iniciar sesión') }}
                </x-button>
            </div>

            <div class="text-center pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500">
                    {{ __('¿No tienes cuenta?') }}
                    <a class="text-blue-800 font-medium hover:text-blue-900 transition-colors"
                       href="{{ route('register') }}">
                        {{ __('Regístrate') }}
                    </a>
                </span>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
