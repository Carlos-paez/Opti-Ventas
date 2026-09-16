<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi Perfil</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Información del perfil --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Información del Perfil</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Actualiza tu nombre y dirección de correo electrónico.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                      :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                      :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label value="Rol" />
                        <div class="mt-1 block w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300">
                            @if ($user->role === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Vendedor
                                </span>
                            @endif
                            <span class="ml-2 text-xs text-gray-500">(solo lectura)</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Guardar</x-primary-button>

                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 2000)"
                               class="text-sm text-gray-600 dark:text-gray-400">
                                Guardado.
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Actualizar contraseña --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Actualizar Contraseña</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.</p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="current_password" value="Contraseña Actual" />
                        <x-text-input id="current_password" name="current_password" type="password"
                                      class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Nueva Contraseña" />
                        <x-text-input id="password" name="password" type="password"
                                      class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar Contraseña" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                      class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Guardar</x-primary-button>

                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 2000)"
                               class="text-sm text-gray-600 dark:text-gray-400">
                                Guardado.
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Eliminar cuenta --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Eliminar Cuenta</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Una vez eliminada tu cuenta, todos los datos serán eliminados permanentemente.</p>

                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
