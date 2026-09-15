<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expense_categories') && !Schema::hasColumn('expense_categories', 'is_rent')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->boolean('is_rent')->default(false)->after('is_operational');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expense_categories') && Schema::hasColumn('expense_categories', 'is_rent')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->dropColumn('is_rent');
            });
        }
    }
};

