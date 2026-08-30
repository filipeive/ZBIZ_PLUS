<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'monthly_salary')) {
                $table->decimal('monthly_salary', 12, 2)->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('monthly_salary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'hire_date')) {
                $table->dropColumn('hire_date');
            }

            if (Schema::hasColumn('users', 'monthly_salary')) {
                $table->dropColumn('monthly_salary');
            }
        });
    }
};
