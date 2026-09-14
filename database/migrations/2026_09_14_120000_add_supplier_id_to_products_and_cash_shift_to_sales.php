<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('category_id')->index();
            });
        }

        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'cash_shift_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unsignedBigInteger('cash_shift_id')->nullable()->after('branch_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'supplier_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('supplier_id');
            });
        }

        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'cash_shift_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('cash_shift_id');
            });
        }
    }
};

