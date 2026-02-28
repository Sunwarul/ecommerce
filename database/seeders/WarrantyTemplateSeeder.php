<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarrantyTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Air Conditioner (Category ID: 3)
            [
                'category_id' => 3,
                'name' => 'AC - Standard Warranty',
                'type' => 'Warranty',
                'duration' => '1 Year',
                'description' => '1 Year Spare Parts, 1 Year Service, 10 Year Compressor',
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'AC - Extended Compressor',
                'type' => 'Warranty',
                'duration' => '1 Year',
                'description' => '1 Year Spare Parts, 1 Year Service, 12 Year Compressor Warranty',
                'is_active' => true,
            ],
            // Refrigerator (Category ID: 6)
            [
                'category_id' => 6,
                'name' => 'Ref - Standard Warranty',
                'type' => 'Warranty',
                'duration' => '1 Year',
                'description' => '1 Year Spare Parts, 1 Year Service, 10 Years Compressor Warranty',
                'is_active' => true,
            ],
            [
                'category_id' => 6,
                'name' => 'Ref - Premium Warranty',
                'type' => 'Warranty',
                'duration' => '2 Years',
                'description' => '2 Years Spare Parts, 2 Years Service, 12 Years Compressor Warranty',
                'is_active' => true,
            ],
            // General
            [
                'category_id' => null,
                'name' => 'General Service Warranty',
                'type' => 'Guaranty',
                'duration' => '6 Months',
                'description' => '6 Months Service Warranty Only',
                'is_active' => true,
            ],
            [
                'category_id' => null,
                'name' => 'No Warranty',
                'type' => 'None',
                'duration' => 'N/A',
                'description' => 'No Warranty Applied',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\WarrantyGuarantee::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
