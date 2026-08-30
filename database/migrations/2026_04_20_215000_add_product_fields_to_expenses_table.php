<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'product_name')) {
                $table->string('product_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('expenses', 'product_quantity')) {
                $table->integer('product_quantity')->nullable()->after('product_name');
            }
            if (!Schema::hasColumn('expenses', 'product_unit_price')) {
                $table->decimal('product_unit_price', 10, 2)->nullable()->after('product_quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'product_name')) {
                $table->dropColumn(['product_name', 'product_quantity', 'product_unit_price']);
            }
        });
    }
};
