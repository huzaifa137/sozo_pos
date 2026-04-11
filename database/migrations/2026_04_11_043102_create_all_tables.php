<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create users table first (if not exists)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Create customers table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });

        // 4. Create stock_batches table
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->string('display_name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
            $table->index('batch_number');
        });

        // 5. Create inventory_items table
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('buying_price', 10, 2);
            
            $table->string('category_code');
            $table->string('batch_code');
            $table->string('category')->nullable();
            $table->string('stock_number')->nullable();
            
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->text('description')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('model')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            
            $table->boolean('published')->default(true);
            $table->boolean('featured')->default(false);
            $table->text('description_long')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->unsignedInteger('views')->default(0);
            
            $table->timestamps();
            
            $table->index('category_code');
            $table->index('batch_code');
            $table->index(['category_code', 'batch_code']);
            $table->index('published');
            $table->index('featured');
            $table->index('quantity');
            
            $table->foreign('category_code')
                  ->references('code')
                  ->on('categories')
                  ->onDelete('restrict');
                  
            $table->foreign('batch_code')
                  ->references('code')
                  ->on('stock_batches')
                  ->onDelete('restrict');
        });

        // 6. Create sales table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('change_given', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'split'])->default('cash');
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['completed', 'refunded', 'voided'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Create sale_items table
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('restrict');
            $table->string('item_name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        // 8. Create orders table if needed
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
                $table->decimal('total', 12, 2)->default(0);
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 9. Create cache table if needed
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('categories');
        // Don't drop users, customers, orders, cache as they might be needed
    }
};