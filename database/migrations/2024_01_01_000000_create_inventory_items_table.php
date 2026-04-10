<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('buying_price', 10, 2);
            $table->string('category');
            $table->string('stock_number');
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->text('description')->nullable();
            // Variants
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('model')->nullable();
            // Batch / expiry (for perishables)
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0); // e.g. 18 for 18%

            $table->boolean('published')->default(true);
            $table->boolean('featured')->default(false);
            $table->text('description_long')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
