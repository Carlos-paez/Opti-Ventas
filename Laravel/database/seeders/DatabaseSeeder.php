<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@optiventas.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Seller user
        User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor@optiventas.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'seller',
            'is_active' => true,
        ]);

        // Categories
        $categories = [
            ['name' => 'Electrónica', 'description' => 'Dispositivos y gadgets', 'color' => '#6366f1'],
            ['name' => 'Ropa', 'description' => 'Vestimenta y accesorios', 'color' => '#ec4899'],
            ['name' => 'Hogar', 'description' => 'Artículos para el hogar', 'color' => '#f59e0b'],
            ['name' => 'Deportes', 'description' => 'Artículos deportivos', 'color' => '#10b981'],
            ['name' => 'Alimentos', 'description' => 'Bebidas y snacks', 'color' => '#ef4444'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat + ['slug' => Str::slug($cat['name'])]);
        }

        // Products
        $products = [
            ['name' => 'Audífonos Bluetooth', 'sku' => 'AUD-001', 'price' => 299.99, 'cost' => 150, 'stock' => 50, 'category_id' => 1],
            ['name' => 'Cable USB-C 2m', 'sku' => 'CAB-001', 'price' => 49.99, 'cost' => 20, 'stock' => 200, 'category_id' => 1],
            ['name' => 'Camiseta Básica', 'sku' => 'ROP-001', 'price' => 199.99, 'cost' => 80, 'stock' => 100, 'category_id' => 2],
            ['name' => 'Jeans Slim Fit', 'sku' => 'ROP-002', 'price' => 599.99, 'cost' => 280, 'stock' => 60, 'category_id' => 2],
            ['name' => 'Lámpara LED', 'sku' => 'HOG-001', 'price' => 349.99, 'cost' => 160, 'stock' => 40, 'category_id' => 3],
            ['name' => 'Set de Cocina 5 pzas', 'sku' => 'HOG-002', 'price' => 899.99, 'cost' => 450, 'stock' => 25, 'category_id' => 3],
            ['name' => 'Balón de Fútbol', 'sku' => 'DEP-001', 'price' => 249.99, 'cost' => 100, 'stock' => 80, 'category_id' => 4],
            ['name' => 'Mancuernas 10kg', 'sku' => 'DEP-002', 'price' => 399.99, 'cost' => 180, 'stock' => 30, 'category_id' => 4],
            ['name' => 'Agua Mineral 600ml', 'sku' => 'ALI-001', 'price' => 15.99, 'cost' => 6, 'stock' => 500, 'category_id' => 5],
            ['name' => 'Chips Fritos 150g', 'sku' => 'ALI-002', 'price' => 29.99, 'cost' => 14, 'stock' => 300, 'category_id' => 5],
            ['name' => 'Mouse Inalámbrico', 'sku' => 'AUD-002', 'price' => 189.99, 'cost' => 90, 'stock' => 75, 'category_id' => 1],
            ['name' => 'Teclado Mecánico', 'sku' => 'AUD-003', 'price' => 699.99, 'cost' => 350, 'stock' => 2, 'category_id' => 1],
        ];

        foreach ($products as $prod) {
            Product::create($prod + [
                'slug' => Str::slug($prod['name']),
                'barcode' => str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);
        }
    }
}
