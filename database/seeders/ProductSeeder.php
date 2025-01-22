<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'น้ำเปล่า',
            'description' => 'น้ำดื่มขวด 500 มล.',
            'price' => 10.00,
            'cost_price' => 7.00,
            'stock_quantity' => 100,
            'restock_level' => 20,
            'category_id' => 1,
        ]);

        Product::create([
            'name' => 'ขนมปัง',
            'description' => 'ขนมปังแผ่น 10 ชิ้น',
            'price' => 25.00,
            'cost_price' => 18.00,
            'stock_quantity' => 50,
            'restock_level' => 10,
            'category_id' => 2,
        ]);
    }
}
