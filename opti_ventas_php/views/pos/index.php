<?php
/** @var array $products */
/** @var int $total */
/** @var int $page */
/** @var int $perPage */
/** @var array $categories */
/** @var array $customers */
/** @var float $taxRate */
?>
<div class="h-[calc(100vh-4rem)] lg:h-[calc(100vh-4rem)] flex flex-col lg:flex-row gap-0 bg-gray-50 dark:bg-gray-900 -m-4 lg:-m-6">

    <div class="flex-1 lg:w-[65%] flex flex-col min-h-0 bg-white dark:bg-gray-800 lg:rounded-none overflow-hidden border-r border-gray-200 dark:border-gray-700">
        <div class="shrink-0 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 space-y-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Buscar productos..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-100 dark:bg-gray-700 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-600 transition"
                       oninput="filterProducts()">
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide" id="categoryFilters">
                <button onclick="filterByCategory(null)" data-category="all"
                        class="category-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-all bg-indigo-600 text-white shadow-sm">
                    Todos
                </button>
                <?php foreach ($categories as $category): ?>
                    <button onclick="filterByCategory('<?= (int) $category['id'] ?>')" data-category="<?= (int) $category['id'] ?>"
                            class="category-pill shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-all bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600">
                        <?= e($category['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4" id="productsContainer">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="productsGrid">
                <?php if (!$products): ?>
                    <div class="col-span-full py-20 text-center">
                        <svg class="mx-auto w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="mt-4 text-gray-500 font-medium">No se encontraron productos</p>
                    </div>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card group relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden cursor-pointer hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-500 hover:-translate-y-0.5 transition-all duration-200"
                         data-id="<?= (int) $product['id'] ?>"
                         data-name="<?= e($product['name']) ?>"
                         data-price="<?= e($product['price']) ?>"
                         data-stock="<?= (int) $product['stock'] ?>"
                         data-category="<?= e($product['category_id'] ?? '') ?>"
                         onclick="addToCart(this)">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <?php if (!empty($product['photo_path'])): ?>
                                <img src="<?= e(url('/storage/' . $product['photo_path'])) ?>" alt="<?= e($product['name']) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-gray-700 dark:to-gray-800">
                                    <svg class="w-10 h-10 text-indigo-300 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white truncate" title="<?= e($product['name']) ?>"><?= e($product['name']) ?></h3>
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400"><?= e(money((float) $product['price'])) ?></span>
                                <span class="stock-badge text-xs px-2 py-0.5 rounded-full font-medium <?= (int) $product['stock'] > 10 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ((int) $product['stock'] > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300') ?>">
                                    <?php if ((int) $product['stock'] > 10): ?>Disponible
                                    <?php elseif ((int) $product['stock'] > 0): ?>Últimas <?= (int) $product['stock'] ?>
                                    <?php else: ?>Agotado<?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-indigo-600/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                            <div class="bg-indigo-600 text-white rounded-full p-2 shadow-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php include views_path('partials/pagination.php'); ?>
        </div>
    </div>

    <div id="cartPanel"
         class="lg:w-[35%] lg:max-w-md bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col fixed inset-x-0 bottom-0 z-40 h-[60vh] lg:relative lg:inset-auto lg:h-auto lg:z-auto rounded-t-2xl lg:rounded-none shadow-2xl lg:shadow-none translate-y-full lg:translate-y-0 transition-transform duration-300">
        <div class="shrink-0 flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Carrito</h2>
                <span id="cartCount" class="hidden inline-flex items-center justify-center min-w-[24px] h-6 px-2 text-xs font-bold bg-indigo-600 text-white rounded-full">0</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="clearCart()" id="clearCartBtn" class="hidden text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Vaciar</button>
                <button onclick="toggleCart()" class="lg:hidden p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="shrink-0 px-5 py-3 border-b border-gray-100 dark:border-gray-700">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cliente (opcional)</label>
            <div class="flex items-center gap-2">
                <select id="customerSelect" class="flex-1 text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Público general</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= (int) $customer['id'] ?>"><?= e($customer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="newCustomerBtn" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 px-3 py-2 text-xs font-semibold text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    Nuevo
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-3" id="cartItems">
            <div id="cartEmpty" class="flex flex-col items-center justify-center h-full text-center py-12">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                </div>
                <p class="text-gray-400 dark:text-gray-500 font-medium">Carrito vacío</p>
                <p class="text-gray-300 dark:text-gray-400 text-sm mt-1">Selecciona un producto para agregar</p>
            </div>
            <div id="cartItemsList" class="space-y-3"></div>
        </div>

        <div class="shrink-0 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-5 py-4 space-y-3">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Descuento</label>
                <div class="relative flex-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"><?= e(money_symbol()) ?></span>
                    <input type="number" id="discountInput" min="0" step="0.01" value="0"
                           class="w-full pl-7 pr-3 py-1.5 text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                           oninput="calculateTotals()">
                </div>
            </div>

            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-600 dark:text-gray-300"><span>Subtotal</span><span id="subtotalDisplay"><?= e(money_symbol()) ?>0.00</span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300"><span>IVA (<?= e(number_format($taxRate, 0)) ?>%)</span><span id="taxDisplay"><?= e(money_symbol()) ?>0.00</span></div>
                <div class="flex justify-between text-gray-600 dark:text-gray-300"><span>Descuento</span><span id="discountDisplay" class="text-red-500">-<?= e(money_symbol()) ?>0.00</span></div>
                <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700"><span>Total</span><span id="totalDisplay"><?= e(money_symbol()) ?>0.00</span></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Método de pago</label>
                <div class="grid grid-cols-4 gap-1.5">
                    <?php
                    $methods = [
                        ['value' => 'efectivo', 'label' => 'Efectivo'],
                        ['value' => 'tarjeta', 'label' => 'Tarjeta'],
                        ['value' => 'transferencia', 'label' => 'Transf.'],
                        ['value' => 'otro', 'label' => 'Otro'],
                    ];
                    ?>
                    <?php foreach ($methods as $i => $method): ?>
                        <label class="payment-method relative cursor-pointer">
                            <input type="radio" name="payment_method" value="<?= e($method['value']) ?>" class="peer sr-only" <?= $i === 0 ? 'checked' : '' ?>>
                            <div class="text-center py-2 px-1 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-xs font-medium text-gray-500 dark:text-gray-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300 transition-all">
                                <?= e($method['label']) ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button id="checkoutBtn" onclick="submitSale()" disabled
                    class="w-full py-3.5 bg-indigo-600 text-white font-bold text-base rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-950/40 hover:bg-indigo-700 active:bg-indigo-800 disabled:bg-gray-300 dark:disabled:bg-gray-600 disabled:shadow-none disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2">
                <span id="checkoutText">Cobrar <?= e(money_symbol()) ?>0.00</span>
            </button>
        </div>
    </div>

    <button id="cartToggle" onclick="toggleCart()"
            class="lg:hidden fixed bottom-4 right-4 z-50 bg-indigo-600 text-white rounded-full p-4 shadow-xl shadow-indigo-300 hover:bg-indigo-700 active:bg-indigo-800 transition-all">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        <span id="mobileCartCount" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold bg-red-500 text-white rounded-full"></span>
    </button>
</div>

<div id="toastContainer" class="fixed top-4 right-4 z-[100] space-y-2"></div>

<div id="newCustomerModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-gray-900/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Agregar cliente</h3>
            <button type="button" id="closeCustomerModal" class="rounded-full p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="newCustomerForm" class="space-y-4">
            <div>
                <label for="newCustomerName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                <input id="newCustomerName" name="name" type="text" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="newCustomerPhone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                <input id="newCustomerPhone" name="phone" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="newCustomerEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo</label>
                <input id="newCustomerEmail" name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="newCustomerAddress" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
                <textarea id="newCustomerAddress" name="address" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <div id="newCustomerErrors" class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300"></div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="cancelCustomerBtn" class="rounded-lg border border-gray-200 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    Guardar cliente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const TAX_RATE = <?= e(number_format($taxRate, 2, '.', '')) ?>;
    const CURRENCY_SYMBOL = <?= json_encode(money_symbol(), JSON_UNESCAPED_UNICODE) ?>;
    const BASE_URL = <?= json_encode(url(''), JSON_UNESCAPED_SLASHES) ?>;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
    let cart = [];

    const customerSelect = document.getElementById('customerSelect');
    const newCustomerModal = document.getElementById('newCustomerModal');
    const newCustomerForm = document.getElementById('newCustomerForm');
    const newCustomerErrors = document.getElementById('newCustomerErrors');

    function openCustomerModal() {
        newCustomerModal.classList.remove('hidden');
        newCustomerModal.classList.add('flex');
        setTimeout(() => document.getElementById('newCustomerName').focus(), 50);
    }

    function closeCustomerModal() {
        newCustomerModal.classList.add('hidden');
        newCustomerModal.classList.remove('flex');
        newCustomerForm.reset();
        newCustomerErrors.classList.add('hidden');
        newCustomerErrors.textContent = '';
    }

    document.getElementById('newCustomerBtn').addEventListener('click', openCustomerModal);
    document.getElementById('closeCustomerModal').addEventListener('click', closeCustomerModal);
    document.getElementById('cancelCustomerBtn').addEventListener('click', closeCustomerModal);
    newCustomerModal.addEventListener('click', (event) => {
        if (event.target === newCustomerModal) {
            closeCustomerModal();
        }
    });

    newCustomerForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(newCustomerForm);
        const params = new URLSearchParams();
        formData.forEach((value, key) => params.append(key, value));

        try {
            const response = await fetch(BASE_URL + '/customers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: params.toString()
            });

            const data = await response.json();

            if (!response.ok) {
                const errorFields = data.errors || {};
                const firstMessage = Object.values(errorFields).find(Boolean);
                newCustomerErrors.textContent = firstMessage ? String(firstMessage) : (data.message || 'No se pudo crear el cliente.');
                newCustomerErrors.classList.remove('hidden');
                return;
            }

            const option = new Option(data.customer.name, String(data.customer.id));
            customerSelect.add(option);
            customerSelect.value = String(data.customer.id);
            closeCustomerModal();
            showToast('Cliente creado y seleccionado', 'success');
        } catch (error) {
            newCustomerErrors.textContent = 'No se pudo crear el cliente. Inténtalo de nuevo.';
            newCustomerErrors.classList.remove('hidden');
        }
    });

    function addToCart(el) {
        const id = parseInt(el.dataset.id);
        const name = el.dataset.name;
        const price = parseFloat(el.dataset.price);
        const stock = parseInt(el.dataset.stock);

        if (stock <= 0) { showToast('Producto agotado', 'error'); return; }

        const existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty >= stock) { showToast('Stock insuficiente', 'error'); return; }
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, stock });
        }

        renderCart();
        showToast(name + ' agregado', 'success');
    }

    function removeFromCart(index) { cart.splice(index, 1); renderCart(); }

    function updateQuantity(index, delta) {
        const item = cart[index];
        const newQty = item.qty + delta;
        if (newQty <= 0) { removeFromCart(index); return; }
        if (newQty > item.stock) { showToast('Stock insuficiente', 'error'); return; }
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
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/70 rounded-xl p-3 group">
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white truncate">${escapeHtml(item.name)}</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-300">${item.qty} × ${CURRENCY_SYMBOL}${item.price.toFixed(2)}</p>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">${CURRENCY_SYMBOL}${(item.qty * item.price).toFixed(2)}</span>
                    <div class="flex items-center gap-1">
                        <button onclick="event.stopPropagation(); updateQuantity(${i}, -1)" class="w-6 h-6 flex items-center justify-center rounded-md bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-300 text-sm font-bold transition-colors">−</button>
                        <span class="w-7 text-center text-sm font-semibold text-gray-900 dark:text-white">${item.qty}</span>
                        <button onclick="event.stopPropagation(); updateQuantity(${i}, 1)" class="w-6 h-6 flex items-center justify-center rounded-md bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-300 text-sm font-bold transition-colors">+</button>
                        <button onclick="event.stopPropagation(); removeFromCart(${i})" class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 dark:text-gray-300 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-300 transition-colors ml-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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

        document.getElementById('subtotalDisplay').textContent = CURRENCY_SYMBOL + subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = CURRENCY_SYMBOL + tax.toFixed(2);
        document.getElementById('discountDisplay').textContent = '-' + CURRENCY_SYMBOL + discount.toFixed(2);
        document.getElementById('totalDisplay').textContent = CURRENCY_SYMBOL + total.toFixed(2);
        document.getElementById('checkoutText').textContent = 'Cobrar ' + CURRENCY_SYMBOL + total.toFixed(2);
        document.getElementById('checkoutBtn').disabled = cart.length === 0;
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
            items: cart.map(item => ({ product_id: item.id, quantity: item.qty }))
        };

        try {
            const response = await fetch(BASE_URL + '/pos/sale', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();

            if (response.ok) {
                showToast('Venta registrada exitosamente', 'success');
                cart = [];
                document.getElementById('discountInput').value = '0';
                renderCart();
                toggleCart(false);
                setTimeout(() => window.location.href = BASE_URL + '/sales/' + data.sale.id, 600);
            } else {
                showToast(data.message || 'Error al procesar la venta', 'error');
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
        const colors = { success: 'bg-emerald-500', error: 'bg-red-500' };
        const icons = {
            success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
        };
        const toast = document.createElement('div');
        toast.className = `flex items-center gap-2 px-4 py-3 rounded-xl text-white text-sm font-medium shadow-xl ${colors[type]} transform transition-all duration-300 translate-x-full opacity-0`;
        toast.innerHTML = `<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">${icons[type]}</svg> ${escapeHtml(message)}`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-x-full', 'opacity-0'));
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
            const isActive = categoryId === null ? pill.dataset.category === 'all' : pill.dataset.category === String(categoryId);
            pill.className = pill.className.replace(
                /bg-indigo-600 text-white shadow-sm|bg-gray-100 text-gray-600 hover:bg-gray-200/,
                isActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            );
        });
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = (categoryId === null || card.dataset.category === String(categoryId)) ? '' : 'none';
        });
    }

    function filterProducts() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = card.dataset.name.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    renderCart();
</script>