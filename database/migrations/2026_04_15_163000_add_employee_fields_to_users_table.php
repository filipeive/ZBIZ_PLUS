<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_code')) {
                $table->string('employee_code', 50)->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title', 100)->nullable()->after('hire_date');
            }

            if (!Schema::hasColumn('users', 'document_number')) {
                $table->string('document_number', 50)->nullable()->after('job_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'document_number')) {
                $table->dropColumn('document_number');
            }

            if (Schema::hasColumn('users', 'job_title')) {
                $table->dropColumn('job_title');
            }

            if (Schema::hasColumn('users', 'employee_code')) {
                $table->dropColumn('employee_code');
            }
        });
    }
};
