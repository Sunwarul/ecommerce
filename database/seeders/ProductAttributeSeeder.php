<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        /* ------------------ Color (previous example) ------------------ */
        $color = ProductAttribute::create([
            'name' => 'color',
            'display_name' => 'Color',
            'type' => 'color_picker',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $color->id, 'value' => 'red', 'display_value' => 'Red', 'color_code' => '#FF0000'],
            ['attribute_id' => $color->id, 'value' => 'blue', 'display_value' => 'Blue', 'color_code' => '#0000FF'],
            ['attribute_id' => $color->id, 'value' => 'green', 'display_value' => 'Green', 'color_code' => '#00FF00'],
        ]);


        /* ------------------ Size ------------------ */
        $size = ProductAttribute::create([
            'name' => 'size',
            'display_name' => 'Size',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $size->id, 'value' => 'S', 'display_value' => 'Small'],
            ['attribute_id' => $size->id, 'value' => 'M', 'display_value' => 'Medium'],
            ['attribute_id' => $size->id, 'value' => 'L', 'display_value' => 'Large'],
            ['attribute_id' => $size->id, 'value' => 'XL', 'display_value' => 'XL'],
        ]);


        /* ------------------ Material ------------------ */
        $material = ProductAttribute::create([
            'name' => 'material',
            'display_name' => 'Material',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $material->id, 'value' => 'cotton', 'display_value' => 'Cotton'],
            ['attribute_id' => $material->id, 'value' => 'leather', 'display_value' => 'Leather'],
            ['attribute_id' => $material->id, 'value' => 'plastic', 'display_value' => 'Plastic'],
            ['attribute_id' => $material->id, 'value' => 'metal', 'display_value' => 'Metal'],
        ]);


        /* ------------------ Brand ------------------ */
        $brand = ProductAttribute::create([
            'name' => 'brand',
            'display_name' => 'Brand',
            'type' => 'text',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $brand->id, 'value' => 'nike', 'display_value' => 'Nike'],
            ['attribute_id' => $brand->id, 'value' => 'adidas', 'display_value' => 'Adidas'],
            ['attribute_id' => $brand->id, 'value' => 'apple', 'display_value' => 'Apple'],
        ]);


        /* ------------------ Weight ------------------ */
        $weight = ProductAttribute::create([
            'name' => 'weight',
            'display_name' => 'Weight',
            'type' => 'number',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $weight->id, 'value' => '0.5', 'display_value' => '0.5 kg'],
            ['attribute_id' => $weight->id, 'value' => '1', 'display_value' => '1 kg'],
            ['attribute_id' => $weight->id, 'value' => '2', 'display_value' => '2 kg'],
        ]);


        /* ------------------ Length ------------------ */
        $length = ProductAttribute::create([
            'name' => 'length',
            'display_name' => 'Length',
            'type' => 'number',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $length->id, 'value' => '10', 'display_value' => '10 cm'],
            ['attribute_id' => $length->id, 'value' => '20', 'display_value' => '20 cm'],
            ['attribute_id' => $length->id, 'value' => '30', 'display_value' => '30 cm'],
        ]);


        /* ------------------ Style ------------------ */
        $style = ProductAttribute::create([
            'name' => 'style',
            'display_name' => 'Style',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $style->id, 'value' => 'modern', 'display_value' => 'Modern'],
            ['attribute_id' => $style->id, 'value' => 'classic', 'display_value' => 'Classic'],
            ['attribute_id' => $style->id, 'value' => 'sport', 'display_value' => 'Sport'],
        ]);


        /* ------------------ Pattern ------------------ */
        $pattern = ProductAttribute::create([
            'name' => 'pattern',
            'display_name' => 'Pattern',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $pattern->id, 'value' => 'solid', 'display_value' => 'Solid'],
            ['attribute_id' => $pattern->id, 'value' => 'striped', 'display_value' => 'Striped'],
            ['attribute_id' => $pattern->id, 'value' => 'checked', 'display_value' => 'Checked'],
        ]);


        /* ------------------ Capacity (for bags, bottles, etc.) ------------------ */
        $capacity = ProductAttribute::create([
            'name' => 'capacity',
            'display_name' => 'Capacity',
            'type' => 'number',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $capacity->id, 'value' => '250', 'display_value' => '250 ml'],
            ['attribute_id' => $capacity->id, 'value' => '500', 'display_value' => '500 ml'],
            ['attribute_id' => $capacity->id, 'value' => '1000', 'display_value' => '1 L'],
        ]);


        /* ------------------ RAM ------------------ */
        $ram = ProductAttribute::create([
            'name' => 'ram',
            'display_name' => 'RAM',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $ram->id, 'value' => '4', 'display_value' => '4 GB'],
            ['attribute_id' => $ram->id, 'value' => '8', 'display_value' => '8 GB'],
            ['attribute_id' => $ram->id, 'value' => '16', 'display_value' => '16 GB'],
        ]);


        /* ------------------ Storage ------------------ */
        $storage = ProductAttribute::create([
            'name' => 'storage',
            'display_name' => 'Storage',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $storage->id, 'value' => '64', 'display_value' => '64 GB'],
            ['attribute_id' => $storage->id, 'value' => '128', 'display_value' => '128 GB'],
            ['attribute_id' => $storage->id, 'value' => '256', 'display_value' => '256 GB'],
        ]);


        /* ------------------ Gender ------------------ */
        $gender = ProductAttribute::create([
            'name' => 'gender',
            'display_name' => 'Gender',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $gender->id, 'value' => 'men', 'display_value' => 'Men'],
            ['attribute_id' => $gender->id, 'value' => 'women', 'display_value' => 'Women'],
            ['attribute_id' => $gender->id, 'value' => 'unisex', 'display_value' => 'Unisex'],
        ]);


        /* ------------------ Season ------------------ */
        $season = ProductAttribute::create([
            'name' => 'season',
            'display_name' => 'Season',
            'type' => 'select',
            'is_active' => true,
        ]);

        ProductAttributeValue::insert([
            ['attribute_id' => $season->id, 'value' => 'summer', 'display_value' => 'Summer'],
            ['attribute_id' => $season->id, 'value' => 'winter', 'display_value' => 'Winter'],
            ['attribute_id' => $season->id, 'value' => 'all', 'display_value' => 'All Season'],
        ]);
    }
}
