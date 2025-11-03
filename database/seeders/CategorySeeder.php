<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Category::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function run(): void
    {
        $parents = [
            'Home Appliances',
            'Kitchen Appliances',
            'Electronics',
            'Personal Care',
            'Cleaning & Laundry',
        ];

        foreach ($parents as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'photo' => null,
                'is_active' => true,
            ]);
        }

        $children = [
            // Home Appliances
            ['Air Conditioner', 1],
            ['Refrigerator', 1],
            ['Ceiling Fan', 1],
            ['Washing Machine', 1],
            ['Water Heater', 1],

            // Kitchen Appliances
            ['Rice Cooker', 2],
            ['Microwave Oven', 2],
            ['Blender & Juicer', 2],
            ['Electric Kettle', 2],
            ['Induction Cooker', 2],

            // Electronics
            ['Television', 3],
            ['Home Theater', 3],
            ['Smart Watch', 3],
            ['Laptop', 3],
            ['Mobile Phone', 3],

            // Personal Care
            ['Hair Dryer', 4],
            ['Hair Straightener', 4],
            ['Electric Shaver', 4],
            ['Trimmer', 4],
            ['Massager', 4],

            // Cleaning & Laundry
            ['Iron Machine', 5],
            ['Vacuum Cleaner', 5],
            ['Steam Cleaner', 5],
            ['Garment Steamer', 5],
            ['Dishwasher', 5],
        ];

        foreach ($children as [$name, $parentId]) {
            Category::create([
                'parent_id' => $parentId,
                'name' => $name,
                'slug' =>  $this->uniqueSlug($name),
                'photo' => null,
                'is_active' => true,
            ]);
        }
    }
}
