<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('sales', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount_type');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'original_unit_price')) {
                $table->decimal('original_unit_price', 10, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('sale_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('sale_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('sale_items', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('discount_percentage');
            }
            if (!Schema::hasColumn('sale_items', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $cols = array_filter(['discount_type', 'discount_reason'], fn($c) => Schema::hasColumn('sales', $c));
            if (!empty($cols)) $table->dropColumn($cols);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $cols = array_filter(['original_unit_price', 'discount_amount', 'discount_percentage', 'discount_type', 'discount_reason'], fn($c) => Schema::hasColumn('sale_items', $c));
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
