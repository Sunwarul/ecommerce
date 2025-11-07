<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Samsung',
                'description' => 'Leading global electronics brand offering TVs, smartphones, and home appliances.',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'LG',
                'description' => 'Renowned for premium TVs, air conditioners, and home appliances.',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sony',
                'description' => 'Global leader in audio, video, gaming, and entertainment products.',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Panasonic',
                'description' => 'Trusted Japanese brand for electronics and home improvement solutions.',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Philips',
                'description' => 'European brand offering personal care, lighting, and healthcare electronics.',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Walton',
                'description' => 'Bangladeshi brand providing affordable and innovative electronic solutions.',
                'photo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
