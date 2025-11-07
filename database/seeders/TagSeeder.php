<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'New Arrival', 'is_active' => true],
            ['name' => 'Best Seller', 'is_active' => true],
            ['name' => 'Discount', 'is_active' => true],
            ['name' => 'Limited Edition', 'is_active' => true],
            ['name' => 'Hot Deal', 'is_active' => true],
            ['name' => 'Featured', 'is_active' => true],
            ['name' => 'Trending', 'is_active' => true],
            ['name' => 'Out of Stock', 'is_active' => false],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
