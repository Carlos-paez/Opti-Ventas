<x-app-layout>
    <div class="h-[calc(100vh-4rem)] lg:h-[calc(100vh-4rem)] flex flex-col lg:flex-row gap-0 bg-gray-50">

        {{-- LEFT PANEL: Products --}}
        <div class="flex-1 lg:w-[65%] flex flex-col min-h-0 bg-white lg:rounded-none overflow-hidden">

            {{-- Search & Filters --}}
            <div class="shrink-0 border-b border-gray-200 bg-white p-4 space-y-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Buscar productos..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:bg-white transition"
                        oninput="filterProducts()"
                    >
                </div>

                {{-- Category pills --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide" id="categoryFilters">
                    <button
                        onclick="filterByCategory(null)"
                        data-category="all"
                        class="category-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-all bg-indigo-600 text-white shadow-sm"
                    >
                        Todos
                    </button>
                    @foreach($categories ?? [] as $category)
                        <button
                            onclick="filterByCategory('{{ $category->id }}')"
                            data-category="{{ $category->id }}"
                            class="category-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-all bg-gray-100 text-gray-600 hover:bg-gray-200"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-4" id="productsContainer">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="productsGrid">
                    @forelse($products as $product)
                        <div
                            class="product-card group relative bg-white border border-gray-200 rounded-xl overflow-hidden cursor-pointer hover:shadow-lg hover:border-indigo-300 hover:-translate-y-0.5 transition-all duration-200"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}"
                            data-stock="{{ $product->stock }}"
                            data-category="{{ $product->category_id ?? '' }}"
                            onclick="addToCart(this)"
                        >
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                @if($product->image)
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-indigo-100">
                                        <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="text-sm font-semibold text-gray-800 truncate" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-lg font-bold text-indigo-600">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="stock-badge text-xs px-2 py-0.5 rounded-full font-medium
                                        {{ $product->stock > 10
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : ($product->stock > 0
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-red-100 text-red-700') }}">
                                        @if($product->stock > 10)
                                            Disponible
                                        @elseif($product->stock > 0)
                                            Últimas {{ $product->stock }}
                                        @else
                                            Agotado
                                        @endif
                                    </span>
                                </div>
                            </div>

                            {{-- Add indicator overlay --}}
                            <div class="absolute inset-0 bg-indigo-600/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <div class="bg-indigo-600 text-white rounded-full p-2 shadow-lg">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <svg class="mx-auto w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="mt-4 text-gray-500 font-medium">No se encontraron productos</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if(method_exists($products, 'links') && $products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT PANEL: Cart --}}
        <div
            id="cartPanel"
            class="lg:w-[35%] lg:max-w-md bg-white border-l border-gray-200 flex flex-col
                   fixed inset-x-0 bottom-0 z-40 h-[60vh] lg:relative lg:inset-auto lg:h-auto lg:z-auto
                   rounded-t-2xl lg:rounded-none shadow-2xl lg:shadow-none
                   translate-y-full lg:translate-y-0 transition-transform duration-300"
        >
            {{-- Cart Header --}}
            <div class="shrink-0 flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-gray-900">Carrito</h2>
                    <span id="cartCount" class="hidden inline-flex items-center justify-center min-w-[24px] h-6 px-2 text-xs font-bold bg-indigo-600 text-white rounded-full">0</span>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        onclick="clearCart()"
                        id="clearCartBtn"
                        class="hidden text-sm text-red-500 hover:text-red-700 font-medium transition-colors"
                    >
                        Vaciar
                    </button>
                    <button onclick="toggleCart()" class="lg:hidden p-1 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Customer Selector --}}
            <div class="shrink-0 px-5 py-3 border-b border-gray-100">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Cliente (opcional)</label>
                <select id="customerSelect" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Público general</option>
                    @foreach($customers ?? [] as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto px-5 py-3" id="cartItems">
                {{-- Empty State --}}
                <div id="cartEmpty" class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium">Carrito vacío</p>
                    <p class="text-gray-300 text-sm mt-1">Selecciona un producto para agregar</p>
                </div>

                {{-- Cart items rendered by JS --}}
                <div id="cartItemsList" class="space-y-3"></div>
            </div>

            {{-- Cart Summary & Checkout --}}
            <div class="shrink-0 border-t border-gray-200 bg-gray-50 px-5 py-4 space-y-3">
                {{-- Discount --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Descuento</label>
                    <div class="relative flex-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                        <input
                            type="number"
                            id="discountInput"
                            min="0"
                            step="0.01"
                            value="0"
                            class="w-full pl-7 pr-3 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                            oninput="calculateTotals()"
                        >
                    </div>
                </div>

                {{-- Totals --}}
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="subtotalDisplay">$0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>IVA ({{ number_format(config('settings.tax_rate', 16), 0) }}%)</span>
                        <span id="taxDisplay">$0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Descuento</span>
                        <span id="discountDisplay" class="text-red-500">-$0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span id="totalDisplay">$0.00</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Método de pago</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <label class="payment-method relative cursor-pointer">
                            <input type="radio" name="payment_method" value="efectivo" class="peer sr-only" checked>
                            <div class="text-center py-2 px-1 rounded-lg border-2 border-gray-200 text-xs font-medium text-gray-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Efectivo
                            </div>
                        </label>
                        <label class="payment-method relative cursor-pointer">
                            <input type="radio" name="payment_method" value="tarjeta" class="peer sr-only">
                            <div class="text-center py-2 px-1 rounded-lg border-2 border-gray-200 text-xs font-medium text-gray-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Tarjeta
                            </div>
                        </label>
                        <label class="payment-method relative cursor-pointer">
                            <input type="radio" name="payment_method" value="transferencia" class="peer sr-only">
                            <div class="text-center py-2 px-1 rounded-lg border-2 border-gray-200 text-xs font-medium text-gray-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Transf.
                            </div>
                        </label>
                        <label class="payment-method relative cursor-pointer">
                            <input type="radio" name="payment_method" value="otro" class="peer sr-only">
                            <div class="text-center py-2 px-1 rounded-lg border-2 border-gray-200 text-xs font-medium text-gray-500 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 transition-all">
                                <svg class="w-5 h-5 mx-auto mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                </svg>
                                Otro
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Cobrar Button --}}
                <button
                    id="checkoutBtn"
                    onclick="submitSale()"
                    disabled
                    class="w-full py-3.5 bg-indigo-600 text-white font-bold text-base rounded-xl shadow-lg shadow-indigo-200
                           hover:bg-indigo-700 active:bg-indigo-800
                           disabled:bg-gray-300 disabled:shadow-none disabled:cursor-not-allowed
                           transition-all duration-200 flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span id="checkoutText">Cobrar $0.00</span>
                </button>
            </div>
        </div>

        {{-- Mobile Cart Toggle --}}
        <button
            id="cartToggle"
            onclick="toggleCart()"
            class="lg:hidden fixed bottom-4 right-4 z-50 bg-indigo-600 text-white rounded-full p-4 shadow-xl shadow-indigo-300
                   hover:bg-indigo-700 active:bg-indigo-800 transition-all"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <span
                id="mobileCartCount"
                class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold bg-red-500 text-white rounded-full"
            ></span>
        </button>
    </div>

    {{-- Toast Container --}}
    <div id="toastContainer" class="fixed top-4 right-4 z-[100] space-y-2"></div>

    <script>
        const TAX_RATE = {{ number_format(config('settings.tax_rate', 16), 2, '.', '') }};
        const STORE_URL = '{{ url("storage") }}';
        let cart = [];

        function addToCart(el) {
            const id = parseInt(el.dataset.id);
            const name = el.dataset.name;
            const price = parseFloat(el.dataset.price);
            const stock = parseInt(el.dataset.stock);

            if (stock <= 0) {
                showToast('Producto agotado', 'error');
                return;
            }

            const existing = cart.find(item => item.id === id);
            if (existing) {
                if (existing.qty >= stock) {
                    showToast('Stock insuficiente', 'error');
                    return;
                }
                existing.qty += 1;
            } else {
                cart.push({ id, name, price, qty: 1, stock });
            }

            renderCart();
            showToast(`${name} agregado`, 'success');
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function updateQuantity(index, delta) {
            const item = cart[index];
            const newQty = item.qty + delta;

            if (newQty <= 0) {
                removeFromCart(index);
                return;
            }

            if (newQty > item.stock) {
                showToast('Stock insuficiente', 'error');
                return;
            }

            item.qty = newQty;
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            if (!confirm('¿Vaciar el carrito?')) return;
            cart = [];
            renderCart();
        }

        function renderCart() {
            const emptyEl = document.getElementById('cartEmpty');
            const listEl = document.getElementById('cartItemsList');
            const countEl = document.getElementById('cartCount');
            const mobileCountEl = document.getElementById('mobileCartCount');
            const clearBtn = document.getElementById('clearCartBtn');
            const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);

            if (cart.length === 0) {
                emptyEl.classList.remove('hidden');
                listEl.innerHTML = '';
                countEl.classList.add('hidden');
                mobileCountEl.textContent = '';
                clearBtn.classList.add('hidden');
            } else {
                emptyEl.classList.add('hidden');
                countEl.textContent = totalItems;
                countEl.classList.remove('hidden');
                mobileCountEl.textContent = totalItems;
                clearBtn.classList.remove('hidden');
            }

            listEl.innerHTML = cart.map((item, i) => `
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3 group">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-800 truncate">${escapeHtml(item.name)}</h4>
                        <p class="text-xs text-gray-500">${item.qty} × $${item.price.toFixed(2)}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="text-sm font-bold text-gray-900">$${(item.qty * item.price).toFixed(2)}</span>
                        <div class="flex items-center gap-1">
                            <button
                                onclick="event.stopPropagation(); updateQuantity(${i}, -1)"
                                class="w-6 h-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600 hover:bg-red-100 hover:text-red-600 text-sm font-bold transition-colors"
                            >−</button>
                            <span class="w-7 text-center text-sm font-semibold">${item.qty}</span>
                            <button
                                onclick="event.stopPropagation(); updateQuantity(${i}, 1)"
                                class="w-6 h-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600 hover:bg-indigo-100 hover:text-indigo-600 text-sm font-bold transition-colors"
                            >+</button>
                            <button
                                onclick="event.stopPropagation(); removeFromCart(${i})"
                                class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:bg-red-100 hover:text-red-600 transition-colors ml-1"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            calculateTotals();
        }

        function calculateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
            const discount = parseFloat(document.getElementById('discountInput').value) || 0;
            const taxableAmount = Math.max(0, subtotal - discount);
            const tax = taxableAmount * (TAX_RATE / 100);
            const total = taxableAmount + tax;

            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('taxDisplay').textContent = '$' + tax.toFixed(2);
            document.getElementById('discountDisplay').textContent = '-$' + discount.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
            document.getElementById('checkoutText').textContent = 'Cobrar $' + total.toFixed(2);

            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.disabled = cart.length === 0;
        }

        async function submitSale() {
            if (cart.length === 0) return;

            const btn = document.getElementById('checkoutBtn');
            const textEl = document.getElementById('checkoutText');
            const originalText = textEl.textContent;

            btn.disabled = true;
            textEl.textContent = 'Procesando...';

            const payload = {
                customer_id: document.getElementById('customerSelect').value || null,
                payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                discount: parseFloat(document.getElementById('discountInput').value) || 0,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.qty,
                    price: item.price,
                })),
            };

            try {
                const response = await fetch('{{ route("pos.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('Venta registrada exitosamente', 'success');
                    cart = [];
                    document.getElementById('discountInput').value = '0';
                    renderCart();
                    toggleCart(false);
                } else {
                    const msg = data.message || 'Error al procesar la venta';
                    showToast(msg, 'error');
                    btn.disabled = false;
                    textEl.textContent = originalText;
                }
            } catch (err) {
                showToast('Error de conexión', 'error');
                btn.disabled = false;
                textEl.textContent = originalText;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
            };
            const icons = {
                success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
                error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            };

            const toast = document.createElement('div');
            toast.className = `flex items-center gap-2 px-4 py-3 rounded-xl text-white text-sm font-medium shadow-xl ${colors[type]} transform transition-all duration-300 translate-x-full opacity-0`;
            toast.innerHTML = `
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">${icons[type]}</svg>
                ${escapeHtml(message)}
            `;
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        function toggleCart(force) {
            const panel = document.getElementById('cartPanel');
            const isHidden = force !== undefined ? force : panel.classList.contains('translate-y-full');

            if (isHidden) {
                panel.classList.remove('translate-y-full');
                panel.classList.add('translate-y-0');
            } else {
                panel.classList.add('translate-y-full');
                panel.classList.remove('translate-y-0');
            }
        }

        function filterByCategory(categoryId) {
            document.querySelectorAll('.category-pill').forEach(pill => {
                const isActive = categoryId === null
                    ? pill.dataset.category === 'all'
                    : pill.dataset.category === String(categoryId);

                pill.className = pill.className.replace(
                    /bg-indigo-600 text-white shadow-sm|bg-gray-100 text-gray-600 hover:bg-gray-200/,
                    isActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                );
            });

            document.querySelectorAll('.product-card').forEach(card => {
                if (categoryId === null || card.dataset.category === String(categoryId)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterProducts() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                card.style.display = name.includes(query) ? '' : 'none';
            });
        }

        renderCart();
    </script>
</x-app-layout>
