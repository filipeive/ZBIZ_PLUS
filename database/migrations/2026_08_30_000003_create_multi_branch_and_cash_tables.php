<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Product Branches (Stock per Branch)
        if (!Schema::hasTable('product_branches')) {
            Schema::create('product_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->integer('stock_quantity')->default(0);
                $table->integer('min_stock_level')->default(5);
                $table->string('location_in_store')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'product_id', 'branch_id']);
                $table->index(['branch_id', 'stock_quantity']);
            });
        }

        // 2. Stock Transfers between Branches
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity');
                $table->string('status')->default('completed'); // pending, completed, cancelled
                $table->date('transfer_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'transfer_date']);
            });
        }

        // 3. Cash Shifts (Fecho e Abertura de Caixa por Filial e Operador)
        if (!Schema::hasTable('cash_shifts')) {
            Schema::create('cash_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('financial_account_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
                $table->timestamp('opened_at');
                $table->timestamp('closed_at')->nullable();
                $table->decimal('opening_balance', 14, 2)->default(0);
                $table->decimal('closing_balance_system', 14, 2)->nullable();
                $table->decimal('closing_balance_actual', 14, 2)->nullable();
                $table->decimal('difference', 14, 2)->nullable();
                $table->string('status')->default('open'); // open, closed
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'branch_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_shifts');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('product_branches');
    }
};
