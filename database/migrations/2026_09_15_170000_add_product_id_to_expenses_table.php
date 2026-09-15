<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'product_id')) {
                    $table->foreignId('product_id')->nullable()->after('notes')->constrained('products')->nullOnDelete();
                }
                if (!Schema::hasColumn('expenses', 'quantity')) {
                    $table->integer('quantity')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('expenses', 'receipt_file_path')) {
                    $table->string('receipt_file_path')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'product_id')) {
                    $table->dropForeign(['product_id']);
                    $table->dropColumn('product_id');
                }
                if (Schema::hasColumn('expenses', 'quantity')) {
                    $table->dropColumn('quantity');
                }
                if (Schema::hasColumn('expenses', 'receipt_file_path')) {
                    $table->dropColumn('receipt_file_path');
                }
            });
        }
    }
};

