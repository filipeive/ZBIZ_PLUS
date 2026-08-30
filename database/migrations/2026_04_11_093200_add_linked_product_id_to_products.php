<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'linked_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('linked_product_id')->nullable()->after('category_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'linked_product_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('linked_product_id');
            });
        }
    }
};
