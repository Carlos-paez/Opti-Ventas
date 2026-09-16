@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nuevo Producto</h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Informaci&oacute;n General</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                               placeholder="Nombre del producto">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('sku') border-red-500 @enderror"
                               placeholder="SKU-001">
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">C&oacute;digo de Barras</label>
                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('barcode') border-red-500 @enderror"
                               placeholder="1234567890123">
                        @error('barcode')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Categor&iacute;a</label>
                        <select id="category_id" name="category_id"
                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Seleccionar categor&iacute;a</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripci&oacute;n</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
                                  placeholder="Descripci&oacute;n del producto">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Precios e Inventario</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Precio de Venta</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                                   class="w-full pl-7 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Costo</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
                            <input type="number" id="cost" name="cost" value="{{ old('cost') }}" step="0.01" min="0"
                                   class="w-full pl-7 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('cost') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('cost')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('stock') border-red-500 @enderror">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock M&iacute;nimo</label>
                        <input type="number" id="stock_min" name="stock_min" value="{{ old('stock_min', 5) }}" min="0"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('stock_min') border-red-500 @enderror">
                        @error('stock_min')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Imagen y Estado</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto del Producto</label>
                        <div class="flex items-center gap-4">
                            <div id="photo-preview" class="w-20 h-20 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="flex-1">
                                <input type="file" id="photo" name="photo" accept="image/*"
                                       class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50 cursor-pointer"
                                       onchange="previewPhoto(this)">
                                <p class="mt-1 text-xs text-gray-400">PNG, JPG, WEBP. Max 2MB</p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5 mt-6">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 focus:ring-primary-500">
                        </div>
                        <label for="is_active" class="ml-3 text-sm mt-5">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Producto activo</span>
                            <span class="block text-gray-500 dark:text-gray-400">El producto estar&aacute; disponible para venta</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                    Crear Producto
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('photo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
