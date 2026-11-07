<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'icon')) {
                $table->string('icon', 80)->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'image_path')) {
                $table->string('image_path', 255)->nullable()->after('icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('products', 'icon')) {
                $cols[] = 'icon';
            }
            if (Schema::hasColumn('products', 'image_path')) {
                $cols[] = 'image_path';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
