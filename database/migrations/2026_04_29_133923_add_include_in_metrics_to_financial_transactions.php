<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('financial_transactions') && !Schema::hasColumn('financial_transactions', 'include_in_metrics')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->boolean('include_in_metrics')->default(true)->after('reversal_of');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('financial_transactions') && Schema::hasColumn('financial_transactions', 'include_in_metrics')) {
            Schema::table('financial_transactions', function (Blueprint $table) {
                $table->dropColumn('include_in_metrics');
            });
        }
    }
};
