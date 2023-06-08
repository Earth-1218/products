<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Generate 50 random products
        for ($i = 1; $i <= 50; $i++) {
            Product::create([
                'name' => 'Product ' . $i,
                'sku' => 'SKU-' . $i,
                'details' => 'Details of Product ' . $i,
                'price' => rand(10, 100),
                'status' => 'active',
                'is_deleted' => 0,
            ]);
        }
    }
}

