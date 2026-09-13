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
        // 1. Atualizar tabela sales
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'invoice_type')) {
                $table->string('invoice_type', 20)->default('cash_invoice')->after('payment_method');
            }
            if (!Schema::hasColumn('sales', 'invoice_number')) {
                $table->string('invoice_number', 50)->nullable()->index()->after('invoice_type');
            }
            if (!Schema::hasColumn('sales', 'due_date')) {
                $table->date('due_date')->nullable()->after('sale_date');
            }
            if (!Schema::hasColumn('sales', 'tax_regime')) {
                $table->string('tax_regime', 30)->default('normal')->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(16.00)->after('tax_regime');
            }
            if (!Schema::hasColumn('sales', 'tax_amount')) {
                $table->decimal('tax_amount', 14, 2)->default(0.00)->after('tax_rate');
            }
            if (!Schema::hasColumn('sales', 'tax_exemption_reason')) {
                $table->string('tax_exemption_reason')->nullable()->after('tax_amount');
            }
            if (!Schema::hasColumn('sales', 'prices_include_tax')) {
                $table->boolean('prices_include_tax')->default(true)->after('tax_exemption_reason');
            }
            if (!Schema::hasColumn('sales', 'customer_nuit')) {
                $table->string('customer_nuit', 25)->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('sales', 'customer_address')) {
                $table->string('customer_address')->nullable()->after('customer_nuit');
            }
            if (!Schema::hasColumn('sales', 'quotation_id')) {
                $table->unsignedBigInteger('quotation_id')->nullable()->index()->after('customer_address');
            }
        });

        // 2. Atualizar tabela sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(16.00)->after('total_price');
            }
            if (!Schema::hasColumn('sale_items', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0.00)->after('tax_rate');
            }
            if (!Schema::hasColumn('sale_items', 'is_tax_exempt')) {
                $table->boolean('is_tax_exempt')->default(false)->after('tax_amount');
            }
        });

        // 3. Atualizar tabela products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_tax_exempt')) {
                $table->boolean('is_tax_exempt')->default(false)->after('unit');
            }
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('is_tax_exempt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_type',
                'invoice_number',
                'due_date',
                'tax_regime',
                'tax_rate',
                'tax_amount',
                'tax_exemption_reason',
                'prices_include_tax',
                'customer_nuit',
                'customer_address',
                'quotation_id',
            ]);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn([
                'tax_rate',
                'tax_amount',
                'is_tax_exempt',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_tax_exempt',
                'tax_rate',
            ]);
        });
    }
};

