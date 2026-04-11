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
                'code' => 'furniture',
                'name' => 'Furniture',
                'display_name' => 'Furniture & Home Decor',
                'description' => 'Furniture and home decoration items',
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
                'code' => 'STK-2026-001',
                'batch_number' => 'STK-2026-001',
                'display_name' => 'STK-2026-001 — April Batch',
                'description' => 'April 2026 Stock Batch',
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