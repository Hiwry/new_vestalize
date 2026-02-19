<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCatalogSeeder extends Seeder
{
    public function run()
    {
        $tenant = Tenant::byCode('ROOSMP')->first();
        if (!$tenant) return;

        // Create Categories
        $cat1 = Category::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'camisetas'],
            ['name' => 'Camisetas', 'active' => true, 'order' => 1]
        );

        $cat2 = Category::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'moletons'],
            ['name' => 'Moletons', 'active' => true, 'order' => 2]
        );

        // Create Products
        $products = [
            [
                'title' => 'Camiseta Premium Algodão',
                'description' => 'Camiseta de alta qualidade, 100% algodão.',
                'price' => 59.90,
                'wholesale_price' => 45.00,
                'wholesale_min_qty' => 10,
                'category_id' => $cat1->id,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Camiseta Oversized Street',
                'description' => 'Estilo oversized para um look moderno.',
                'price' => 79.90,
                'wholesale_price' => 55.00,
                'wholesale_min_qty' => 6,
                'category_id' => $cat1->id,
                'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Moletom Canguru Basic',
                'description' => 'Moletom com bolso frontal e capuz.',
                'price' => 129.90,
                'wholesale_price' => 99.00,
                'wholesale_min_qty' => 5,
                'category_id' => $cat2->id,
                'image' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Moletom Crewneck Minimal',
                'description' => 'Moletom gola careca, sem capuz.',
                'price' => 119.90,
                'wholesale_price' => 89.00,
                'wholesale_min_qty' => 5,
                'category_id' => $cat2->id,
                'image' => 'https://images.unsplash.com/photo-1578587018452-892bacefd3f2?auto=format&fit=crop&q=80&w=800',
            ],
            [
                'title' => 'Polo Industrial Work',
                'description' => 'Camiseta gola polo resistente.',
                'price' => 69.90,
                'wholesale_price' => 49.00,
                'wholesale_min_qty' => 12,
                'category_id' => $cat1->id,
                'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c7176c8?auto=format&fit=crop&q=80&w=800',
            ],
        ];

        foreach ($products as $pData) {
            $imageUrl = $pData['image'];
            unset($pData['image']);

            $product = Product::create(array_merge($pData, [
                'tenant_id' => $tenant->id,
                'active' => true,
                'show_in_catalog' => true,
                'sku' => strtoupper(Str::random(8)),
            ]));

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imageUrl,
                'is_primary' => true,
                'order' => 0,
            ]);
        }
    }
}
