<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['customer', 'user'])
            ->search($request->string('search'))
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date('to'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale): View
    {
        $sale->load(['customer', 'user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function store(Request $request): RedirectResponse|Sale
    {
        $validated = $this->validateSale($request);

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

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Venta registrada correctamente.');
    }

    public function void(Sale $sale): RedirectResponse
    {
        abort_unless(auth()->user()?->is_admin, 403, 'No tienes permisos para anular ventas.');

        if ($sale->is_voided) {
            return back()->withErrors(['sale' => 'Esta venta ya fue anulada.']);
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items()->with('product')->get() as $item) {
                $item->product?->increaseStock(
                    $item->quantity,
                    "Anulación venta #{$sale->id}",
                    auth()->id(),
                );
            }

            $sale->update(['status' => 'voided']);
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venta anulada y stock restaurado.');
    }

    protected function validateSale(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,card,transfer,other'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
