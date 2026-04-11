<?php
// database/seeders/CategoryAndStockBatchSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\StockBatch;
use Illuminate\Database\Seeder;

class CategoryAndStockBatchSeeder extends Seeder
{
    public function run()
    {
        // Seed Categories
        $categories = [
            [
                'code' => 'electronics',
                'name' => 'Electronics',
                'display_name' => 'Electronics & Gadgets',
                'description' => 'Electronic devices and accessories',
                'is_active' => true
            ],
            [
                'code' => 'clothing',
                'name' => 'Clothing',
                'display_name' => 'Clothing & Apparel',
                'description' => 'Clothing items and accessories',
                'is_active' => true
            ],
            [
                'code' => 'food_beverage',
                'name' => 'Food & Beverage',
                'display_name' => 'Food & Beverages',
                'description' => 'Food items and drinks',
                'is_active' => true
            ],
            [
                'code' => 'furniture',
                'name' => 'Furniture',
                'display_name' => 'Furniture & Home Decor',
                'description' => 'Furniture and home decoration items',
                'is_active' => true
            ],
            [
                'code' => 'stationery',
                'name' => 'Stationery',
                'display_name' => 'Stationery & Office',
                'description' => 'Office supplies and stationery',
                'is_active' => true
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        // Seed Stock Batches
        $batches = [
            [
                'code' => 'STK-2024-001',
                'batch_number' => 'STK-2024-001',
                'display_name' => 'STK-2024-001 — January Batch',
                'description' => 'January 2024 Stock Batch',
                'is_active' => true
            ],
            [
                'code' => 'STK-2024-002',
                'batch_number' => 'STK-2024-002',
                'display_name' => 'STK-2024-002 — March Batch',
                'description' => 'March 2024 Stock Batch',
                'is_active' => true
            ],
            [
                'code' => 'STK-2024-003',
                'batch_number' => 'STK-2024-003',
                'display_name' => 'STK-2024-003 — June Batch',
                'description' => 'June 2024 Stock Batch',
                'is_active' => true
            ],
            [
                'code' => 'STK-2024-004',
                'batch_number' => 'STK-2024-004',
                'display_name' => 'STK-2024-004 — September Batch',
                'description' => 'September 2024 Stock Batch',
                'is_active' => true
            ],
            [
                'code' => 'STK-2024-005',
                'batch_number' => 'STK-2024-005',
                'display_name' => 'STK-2024-005 — December Batch',
                'description' => 'December 2024 Stock Batch',
                'is_active' => true
            ],
        ];

        foreach ($batches as $batch) {
            StockBatch::updateOrCreate(
                ['code' => $batch['code']],
                $batch
            );
        }

        $this->command->info('Categories and Stock Batches seeded successfully!');
        $this->command->info('Categories created: ' . Category::count());
        $this->command->info('Stock Batches created: ' . StockBatch::count());
    }
}