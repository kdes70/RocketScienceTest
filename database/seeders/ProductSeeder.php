<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\PropertyValue;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::factory(100)->create();

        $propertyValues = PropertyValue::all();

        foreach ($products as $product) {
            // Привязываем случайные значения свойств к каждому продукту
            $randomPropertyValues = $propertyValues->random(rand(2, 5));
            $product->propertyValues()->attach($randomPropertyValues->pluck('id')->toArray());
        }
    }
}
