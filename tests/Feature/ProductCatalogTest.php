<?php

namespace Feature;

use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_get_paginated_products()
    {
        $this->seed();

        $response = $this->getJson(route('api.v1.products'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'quantity'],
                ],
                'links',
                'total',
            ])
            ->assertJsonCount(40, 'data'); // Проверяем, что по умолчанию возвращается 40 товаров
    }

    public function test_can_filter_products_by_property()
    {
        $property = Property::factory()->create(['name' => 'color2']);
        $redValue = PropertyValue::factory()->create(['property_id' => $property->id, 'value' => 'red']);
        $blueValue = PropertyValue::factory()->create(['property_id' => $property->id, 'value' => 'blue']);

        $redProduct = Product::factory()->create(['name' => 'Red Product']);
        $redProduct->propertyValues()->attach($redValue->id);

        $blueProduct = Product::factory()->create(['name' => 'Blue Product']);
        $blueProduct->propertyValues()->attach($blueValue->id);

        $response = $this->getJson(route('api.v1.products').'?properties[color2][]=red');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Red Product');
    }

    public function test_can_filter_products_by_price_range()
    {
        Product::factory()->create(['name' => 'Cheap Product', 'price' => 10]);
        Product::factory()->create(['name' => 'Expensive Product', 'price' => 100]);

        $response = $this->getJson(route('api.v1.products').'?price[min]=50&price[max]=150');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Expensive Product');
    }

    public function test_can_sort_products()
    {
        Product::factory()->create(['name' => 'Z Product', 'price' => 10]);
        Product::factory()->create(['name' => 'A Product', 'price' => 20]);

        $response = $this->getJson(route('api.v1.products').'?sort_by=name&sort_direction=asc');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'A Product')
            ->assertJsonPath('data.1.name', 'Z Product');
    }

    public function test_invalid_filter_returns_error()
    {
        $response = $this->getJson(route('api.v1.products').'?price[min]=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price.min']);
    }

    public function test_can_specify_items_per_page()
    {
        Product::factory(15)->create();

        $response = $this->getJson(route('api.v1.products').'?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_cannot_exceed_maximum_items_per_page()
    {
        $response = $this->getJson(route('api.v1.products').'?per_page=101');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_can_filter_by_multiple_properties()
    {
        $colorProperty = Property::factory()->create(['name' => 'color']);
        $redValue = PropertyValue::factory()->create(['property_id' => $colorProperty->id, 'value' => 'red']);

        $sizeProperty = Property::factory()->create(['name' => 'size']);
        $largeValue = PropertyValue::factory()->create(['property_id' => $sizeProperty->id, 'value' => 'large']);

        $product = Product::factory()->create(['name' => 'Large Red Product']);
        $product->propertyValues()->attach([$redValue->id, $largeValue->id]);

        Product::factory()->create(['name' => 'Small Red Product']);

        $response = $this->getJson(route('api.v1.products').'?properties[color][]=red&properties[size][]=large');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Large Red Product');
    }

    public function test_empty_result_set()
    {
        $response = $this->getJson(route('api.v1.products').'?properties[color][]=nonexistent');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
