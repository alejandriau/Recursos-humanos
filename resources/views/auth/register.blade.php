<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="flex justify-center">
                <x-authentication-card-logo class="h-16 w-16" />
            </div>
        </x-slot>

        <x-validation-errors class="mb-6" />

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <x-label for="name" value="{{ __('Nombre') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="name"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         type="text"
                         name="name"
                         :value="old('name')"
                         required
                         autofocus
                         autocomplete="name"/>
            </div>

            <div class="space-y-2">
                <x-label for="email" value="{{ __('Email') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="email"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         type="email"
                         name="email"
                         :value="old('email')"
                         required
                         autocomplete="username"/>
            </div>

            <div class="space-y-2">
                <x-label for="password" value="{{ __('Contraseña') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="password"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         type="password"
                         name="password"
                         required
                         autocomplete="new-password"/>
            </div>

            <div class="space-y-2">
                <x-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" class="text-sm font-medium text-gray-700" />
                <x-input id="password_confirmation"
                         class="w-full border-gray-300 focus:border-blue-800 focus:ring-blue-800/20"
                         type="password"
                         name="password_confirmation"
                         required
                         autocomplete="new-password"/>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4 pt-2">
                    <label for="terms" class="flex items-start space-x-3 cursor-pointer">
                        <x-checkbox name="terms"
                                  id="terms"
                                  required
                                  class="mt-1 border-gray-300 text-blue-800 focus:ring-blue-800/30" />
                        <div class="text-sm text-gray-600">
                            {!! __('Acepto los :terms_of_service y :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-blue-800 hover:text-blue-900 underline transition-colors">'.__('Términos del Servicio').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-blue-800 hover:text-blue-900 underline transition-colors">'.__('Política de Privacidad').'</a>',
                            ]) !!}
                        </div>
                    </label>
                </div>
            @endif

            <div class="pt-4">
                <x-button class="w-full justify-center bg-blue-800 hover:bg-blue-900 focus:bg-blue-900 active:bg-blue-950 text-white">
                    {{ __('Registrarse') }}
                </x-button>
            </div>

            <div class="text-center pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500">
                    {{ __('¿Ya tienes cuenta?') }}
                    <a class="text-blue-800 font-medium hover:text-blue-900 transition-colors"
                       href="{{ route('login') }}">
                        {{ __('Inicia sesión') }}
                    </a>
                </span>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
