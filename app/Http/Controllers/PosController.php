<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::active()
            ->with('category')
            ->search($request->string('search'))
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('products', 'categories', 'customers'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,transfer,other'],
            'notes' => ['nullable', 'string'],
        ]);

        $sale = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para el producto \"{$product->name}\" (disponible: {$product->stock}).",
                    ]);
                }

                $lineTotal = (float) $product->price * $item['quantity'];
                $subtotal += $lineTotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'line_total' => round($lineTotal, 2),
                ];
            }

            $taxRate = (float) ($validated['tax_rate'] ?? DB::table('settings')->where('key', 'tax_rate')->value('value') ?? 0);
            $discount = (float) ($validated['discount'] ?? 0);
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = round($subtotal - $discount + $tax, 2);

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $sale->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']->price,
                    'line_total' => $item['line_total'],
                ]);

                $item['product']->decreaseStock(
                    $item['quantity'],
                    "Venta #{$sale->id}",
                    auth()->id(),
                );
            }

            return $sale;
        });

        return response()->json([
            'success' => true,
            'message' => 'Venta registrada correctamente.',
            'sale' => $sale->load('items.product'),
        ], 201);
    }
}
