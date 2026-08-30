<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Customers Table
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('name');
                $table->string('phone', 30)->nullable()->index();
                $table->string('email')->nullable();
                $table->string('nuit', 15)->nullable()->index();
                $table->string('document_type', 20)->default('BI'); // BI, DIRE, Passaporte, NUIT
                $table->string('document_number', 50)->nullable();
                $table->string('address')->nullable();
                $table->decimal('credit_limit', 14, 2)->default(0);
                $table->decimal('current_debt', 14, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
                $table->index(['tenant_id', 'name']);
            });
        }

        // 2. Suppliers Table
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email')->nullable();
                $table->string('nuit', 15)->nullable()->index();
                $table->string('address')->nullable();
                $table->text('bank_details')->nullable();
                $table->string('payment_terms')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        // 3. Link customer_id to sales, debts, orders
        foreach (['sales', 'debts', 'orders'] as $tbl) {
            if (Schema::hasTable($tbl) && !Schema::hasColumn($tbl, 'customer_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('customer_id')->nullable()->after('user_id')->index();
                });
            }
        }

        // 4. Link supplier_id to expenses
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'supplier_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('expense_category_id')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['sales', 'debts', 'orders'] as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'customer_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropColumn('customer_id');
                });
            }
        }

        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'supplier_id')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('supplier_id');
            });
        }

        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};
