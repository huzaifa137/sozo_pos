<?php
// database/migrations/2026_04_11_043102_create_all_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'manager', 'cashier'])->default('cashier');
            $table->boolean('is_active')->default(true);
            $table->string('pin', 6)->nullable(); // quick PIN login
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Create customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->string('loyalty_tier')->default('bronze'); // bronze, silver, gold
            $table->timestamps();
        });

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

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('category_code'); // FK to categories.code
            $table->string('name');
            $table->string('display_name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_code')
                ->references('code')
                ->on('categories')
                ->onDelete('cascade');

            $table->index('category_code');
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

        // 5. Create inventory_items table with relationships
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('buying_price', 10, 2);

            // New foreign key fields
            $table->string('category_code');
            $table->string('batch_code');

            // Backward compatibility fields
            $table->string('category')->nullable();
            $table->string('stock_number')->nullable();

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

            // Indexes
            $table->index('category_code');
            $table->index('batch_code');
            $table->index(['category_code', 'batch_code']);
            $table->index('published');
            $table->index('featured');
            $table->index('quantity');

            // Foreign key constraints
            $table->foreign('category_code')
                ->references('code')
                ->on('categories')
                ->onDelete('restrict');

            $table->foreign('batch_code')
                ->references('code')
                ->on('stock_batches')
                ->onDelete('restrict');

            $table->string('subcategory_code')->nullable();

            $table->foreign('subcategory_code')
                ->references('code')
                ->on('subcategories')
                ->onDelete('restrict');

            $table->index('subcategory_code');
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
            $table->string('payment_reference')->nullable(); // mobile money ref / card last4
            $table->enum('status', ['completed', 'refunded', 'voided'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Create sale_items table
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('restrict');
            $table->string('item_name'); // snapshot at time of sale
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        // 8. Create orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            // Delivery
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->enum('delivery_method', ['pickup', 'delivery'])->default('pickup');
            $table->decimal('delivery_fee', 8, 2)->default(0);

            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Payment
            $table->enum('payment_method', ['cash_on_delivery', 'mobile_money', 'card_on_delivery'])->default('cash_on_delivery');
            $table->string('payment_reference')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

            // Fulfilment
            $table->enum('status', ['pending', 'confirmed', 'processing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('channel')->default('online'); // online | pos
            $table->timestamps();
        });

        // 9. Create order_items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('restrict');
            $table->string('item_name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        // 10. Create cache table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->bigInteger('expiration')->index();
        });

        // 11. Create cache_locks table
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->bigInteger('expiration')->index();
        });
    }

    public function down(): void
    {
        // Drop in reverse order to avoid foreign key conflicts
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
    }
};