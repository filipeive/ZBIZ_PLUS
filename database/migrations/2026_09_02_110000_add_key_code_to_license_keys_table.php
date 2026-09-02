<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('license_keys') && !Schema::hasColumn('license_keys', 'key_code')) {
            Schema::table('license_keys', function (Blueprint $table) {
                $table->string('key_code', 64)->nullable()->unique()->after('issued_by_user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('license_keys') && Schema::hasColumn('license_keys', 'key_code')) {
            Schema::table('license_keys', function (Blueprint $table) {
                $table->dropColumn('key_code');
            });
        }
    }
};
