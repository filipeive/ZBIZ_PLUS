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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('promotional_price', 12, 2)->nullable()->after('selling_price');
            $table->boolean('is_on_promotion')->default(false)->after('promotional_price');
            $table->decimal('promotion_discount_percent', 5, 2)->nullable()->after('is_on_promotion');
            $table->timestamp('promotion_ends_at')->nullable()->after('promotion_discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['promotional_price', 'is_on_promotion', 'promotion_discount_percent', 'promotion_ends_at']);
        });
    }
};
