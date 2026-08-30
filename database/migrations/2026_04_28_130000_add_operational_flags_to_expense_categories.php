<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expense_categories') && !Schema::hasColumn('expense_categories', 'is_operational')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->boolean('is_operational')->default(true)->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expense_categories') && Schema::hasColumn('expense_categories', 'is_operational')) {
            Schema::table('expense_categories', function (Blueprint $table) {
                $table->dropColumn('is_operational');
            });
        }
    }
};
