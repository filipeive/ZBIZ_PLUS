<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('restaurant_table_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('restaurant_tables')
                ->nullOnDelete();
            $table->index(['tenant_id', 'branch_id', 'restaurant_table_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_table_id']);
            $table->dropIndex(['tenant_id', 'branch_id', 'restaurant_table_id']);
            $table->dropColumn('restaurant_table_id');
        });
    }
};