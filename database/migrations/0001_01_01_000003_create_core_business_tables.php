<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categorias
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Categorias de Despesas
        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_operational')->default(true);
                $table->timestamps();
            });
        }

        // 3. Produtos
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->unsignedBigInteger('linked_product_id')->nullable();
                $table->string('name');
                $table->string('original_name')->nullable();
                $table->text('description')->nullable();
                $table->string('type')->default('product'); // product, service
                $table->decimal('purchase_price', 12, 2)->default(0);
                $table->decimal('selling_price', 12, 2)->default(0);
                $table->integer('stock_quantity')->default(0);
                $table->integer('min_stock_level')->default(5);
                $table->string('unit')->default('unidade');
                $table->string('barcode')->nullable()->index();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_deleted')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 4. Vendas
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('customer_name')->default('Cliente Avulso');
                $table->string('customer_phone')->nullable();
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('discount_amount', 14, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->nullable();
                $table->string('discount_type')->nullable(); // fixed, percentage, general, mixed
                $table->string('discount_reason')->nullable();
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->string('payment_method')->default('cash'); // cash, card, transfer, credit, mpesa, emola
                $table->text('notes')->nullable();
                $table->date('sale_date')->nullable();
                $table->timestamps();
            });
        }

        // 5. Itens da Venda
        if (!Schema::hasTable('sale_items')) {
            Schema::create('sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('original_unit_price', 12, 2)->default(0);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->nullable();
                $table->string('discount_type')->nullable();
                $table->string('discount_reason')->nullable();
                $table->timestamps();
            });
        }

        // 6. Itens de Dívida
        if (!Schema::hasTable('debt_items')) {
            Schema::create('debt_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('debt_id')->index();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // 7. Movimentações de Stock
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('movement_type'); // in, out, adjustment
                $table->integer('quantity');
                $table->string('reason')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->date('movement_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 8. Despesas
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
                $table->unsignedBigInteger('financial_account_id')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('amount', 14, 2);
                $table->date('expense_date');
                $table->string('description');
                $table->text('notes')->nullable();
                $table->string('payment_method')->default('cash');
                $table->string('receipt_path')->nullable();
                $table->string('product_name')->nullable();
                $table->integer('product_quantity')->nullable();
                $table->decimal('product_unit_price', 12, 2)->nullable();
                $table->boolean('is_operational')->default(true);
                $table->timestamps();
            });
        }

        // 9. Actividade de Utilizador
        if (!Schema::hasTable('user_activities')) {
            Schema::create('user_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->text('description');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 10. Senhas Temporárias
        if (!Schema::hasTable('temporary_passwords')) {
            Schema::create('temporary_passwords', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('password');
                $table->timestamp('expires_at');
                $table->boolean('is_used')->default(false);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_passwords');
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('debt_items');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('products');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('categories');
    }
};
