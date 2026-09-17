<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;

final class PosController
{
    private const METHOD_MAP = [
        'efectivo' => 'cash',
        'cash' => 'cash',
        'tarjeta' => 'card',
        'card' => 'card',
        'transferencia' => 'transfer',
        'transfer' => 'transfer',
        'otro' => 'other',
        'other' => 'other',
    ];

    public function index(): void
    {
        $page = max(1, (int) query('page', 1));
        $perPage = 100;

        $categoryId = query('category_id');
        $categoryId = $categoryId !== null && $categoryId !== '' ? (int) $categoryId : null;

        $paged = Product::activeForPos($categoryId, $page, $perPage);

        View::render('pos/index', [
            'products' => $paged['items'],
            'total' => $paged['total'],
            'page' => $page,
            'perPage' => $perPage,
            'categories' => Category::allOrdered(),
            'customers' => Customer::allOrdered(),
            'taxRate' => (float) (Setting::get('tax_rate', 16) ?? 16),
            'currencySymbol' => money_symbol(),
        ]);
    }

    public function store(): void
    {
        $data = json_input();

        $errors = [];

        if (empty($data['payment_method']) || !is_string($data['payment_method'])) {
            $errors['payment_method'] = 'El método de pago es obligatorio.';
        } elseif (!isset(self::METHOD_MAP[strtolower($data['payment_method'])])) {
            $errors['payment_method'] = 'El método de pago seleccionado no es válido.';
        }

        $customerId = $data['customer_id'] ?? null;
        if ($customerId !== null && (int) $customerId !== 0) {
            if (Customer::find((int) $customerId) === null) {
                $errors['customer_id'] = 'El cliente seleccionado no existe.';
            } else {
                $customerId = (int) $customerId;
            }
        } else {
            $customerId = null;
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            $errors['items'] = 'Debes agregar al menos un producto.';
        }

        if ($errors) {
            json_response(['success' => false, 'message' => 'Datos de venta inválidos.', 'errors' => $errors], 422);
        }

        $discount = isset($data['discount']) ? max(0, (float) $data['discount']) : 0.0;
        $taxRate = isset($data['tax_rate']) && $data['tax_rate'] !== ''
            ? max(0, (float) $data['tax_rate'])
            : (float) (Setting::get('tax_rate', 16) ?? 16);
        $paymentMethod = self::METHOD_MAP[strtolower((string) $data['payment_method'])] ?? 'other';
        $notes = !empty($data['notes']) ? (string) $data['notes'] : null;

        try {
            $saleId = Database::transaction(function () use ($data, $customerId, $discount, $taxRate, $paymentMethod, $notes) {
                $subtotal = 0.0;
                $lines = [];

                foreach ($data['items'] as $item) {
                    $productId = (int) ($item['product_id'] ?? 0);
                    $quantity = (int) ($item['quantity'] ?? 0);

                    $product = $productId > 0 ? Product::find($productId) : null;

                    if ($product === null) {
                        throw new \DomainException('Producto no encontrado.');
                    }

                    if ((int) $product['is_active'] !== 1) {
                        throw new \DomainException("El producto \"{$product['name']}\" no está disponible.");
                    }

                    if ($quantity < 1) {
                        throw new \DomainException('La cantidad debe ser al menos 1.');
                    }

                    if ((int) $product['stock'] < $quantity) {
                        throw new \DomainException(
                            "Stock insuficiente para el producto \"{$product['name']}\" (disponible: {$product['stock']})."
                        );
                    }

                    $lineTotal = (float) $product['price'] * $quantity;
                    $subtotal += $lineTotal;

                    $lines[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'line_total' => round($lineTotal, 2),
                    ];
                }

                $tax = round($subtotal * ($taxRate / 100), 2);
                $total = round($subtotal - $discount + $tax, 2);

                $saleId = Sale::create([
                    'user_id' => Auth::id(),
                    'customer_id' => $customerId,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $total,
                    'payment_method' => $paymentMethod,
                    'status' => Sale::STATUS_COMPLETED,
                    'notes' => $notes,
                ]);

                foreach ($lines as $line) {
                    SaleItem::create([
                        'sale_id' => $saleId,
                        'product_id' => $line['product']['id'],
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['product']['price'],
                        'line_total' => $line['line_total'],
                    ]);

                    Product::adjustStock(
                        (int) $line['product']['id'],
                        $line['quantity'],
                        'out',
                        "Venta #{$saleId}",
                        Auth::id(),
                    );
                }

                return $saleId;
            });
        } catch (\DomainException $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $sale = Sale::find($saleId);
        $sale['items'] = Sale::items($saleId);

        json_response([
            'success' => true,
            'message' => 'Venta registrada correctamente.',
            'sale' => $sale,
        ], 201);
    }
}