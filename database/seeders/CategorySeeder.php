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
        // Define parent categories
        $parents = [
            'Home Appliances' => [
                'Air Conditioner' => ['Split Type', 'Window Type'],
                'Refrigerator' => ['Single Door', 'Double Door', 'Mini Fridge'],
                'Ceiling Fan' => ['Decorative Fan', 'Energy Saving Fan'],
            ],
            'Kitchen Appliances' => [
                'Rice Cooker' => ['Small', 'Medium', 'Large'],
                'Microwave Oven' => ['Convection', 'Grill'],
                'Blender & Juicer' => ['Hand Blender', 'Juicer'],
            ],
            'Electronics' => [
                'Television' => ['LED TV', 'Smart TV', '4K TV'],
                'Laptop' => ['Gaming Laptop', 'Ultrabook', 'Business Laptop'],
            ],
            'Personal Care' => [
                'Hair Dryer' => ['Compact Dryer', 'Professional Dryer'],
                'Trimmer' => ['Beard Trimmer', 'Body Groomer'],
            ],
            'Cleaning & Laundry' => [
                'Vacuum Cleaner' => ['Cordless', 'Robotic', 'Wet & Dry'],
                'Washing Machine' => ['Front Load', 'Top Load', 'Twin Tub'],
            ],
        ];

        // Loop through all levels
        foreach ($parents as $parentName => $children) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => $this->uniqueSlug($parentName),
                'photo' => null,
                'is_active' => true,
            ]);

            foreach ($children as $childName => $grandChildren) {
                $child = Category::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'slug' => $this->uniqueSlug($childName),
                    'photo' => null,
                    'is_active' => true,
                ]);

                foreach ($grandChildren as $grandChildName) {
                    Category::create([
                        'parent_id' => $child->id,
                        'name' => $grandChildName,
                        'slug' => $this->uniqueSlug($grandChildName),
                        'photo' => null,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
