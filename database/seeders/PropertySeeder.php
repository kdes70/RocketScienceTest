<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $properties = [
            'color' => ['red', 'blue', 'green', 'yellow', 'black', 'white'],
            'size' => ['small', 'medium', 'large', 'x-large'],
            'material' => ['cotton', 'polyester', 'wool', 'silk'],
            'brand' => ['BrandA', 'BrandB', 'BrandC', 'BrandD'],
        ];

        foreach ($properties as $propertyName => $values) {
            $property = Property::create(['name' => $propertyName]);
            foreach ($values as $value) {
                PropertyValue::create([
                    'property_id' => $property->id,
                    'value' => $value,
                ]);
            }
        }
    }
}
