<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="business_name" value="Nombre del Negocio" />
                        <x-text-input id="business_name" name="business_name" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('business_name', $settings['business_name'] ?? '')" />
                        <p class="mt-1 text-xs text-gray-500">Nombre que aparecerá en los recibos y reportes.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('business_name')" />
                    </div>

                    <div>
                        <x-input-label for="tax_rate" value="Tasa de Impuesto (%)" />
                        <x-text-input id="tax_rate" name="tax_rate" type="number"
                                      step="0.01" min="0" max="100"
                                      class="mt-1 block w-full"
                                      :value="old('tax_rate', $settings['tax_rate'] ?? '')" />
                        <p class="mt-1 text-xs text-gray-500">Porcentaje de impuesto aplicado a las ventas (ej. 16 para IVA 16%).</p>
                        <x-input-error class="mt-2" :messages="$errors->get('tax_rate')" />
                    </div>

                    <div>
                        <x-input-label for="currency" value="Moneda" />
                        <select id="currency" name="currency"
                                class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                            <option value="MXN" {{ old('currency', $settings['currency'] ?? '') === 'MXN' ? 'selected' : '' }}>MXN - Peso Mexicano</option>
                            <option value="USD" {{ old('currency', $settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                            <option value="EUR" {{ old('currency', $settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="COP" {{ old('currency', $settings['currency'] ?? '') === 'COP' ? 'selected' : '' }}>COP - Peso Colombiano</option>
                            <option value="ARS" {{ old('currency', $settings['currency'] ?? '') === 'ARS' ? 'selected' : '' }}>ARS - Peso Argentino</option>
                            <option value="CLP" {{ old('currency', $settings['currency'] ?? '') === 'CLP' ? 'selected' : '' }}>CLP - Peso Chileno</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Moneda utilizada para los precios y reportes.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                    </div>

                    <div>
                        <x-input-label for="receipt_footer" value="Pie de Recibo" />
                        <textarea id="receipt_footer" name="receipt_footer" rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm"
                                  placeholder="¡Gracias por su compra!">{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Mensaje que se imprime al final de cada recibo.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('receipt_footer')" />
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <x-primary-button>Guardar Configuración</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
