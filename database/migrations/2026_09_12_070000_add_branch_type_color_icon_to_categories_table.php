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
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('tenant_id')->constrained('branches')->nullOnDelete();
                }
                if (!Schema::hasColumn('categories', 'type')) {
                    $table->string('type')->default('product')->after('description');
                }
                if (!Schema::hasColumn('categories', 'color')) {
                    $table->string('color', 7)->default('#10b981')->after('type');
                }
                if (!Schema::hasColumn('categories', 'icon')) {
                    $table->string('icon', 100)->default('fa-tag')->after('color');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['branch_id', 'type', 'color', 'icon'] as $col) {
                    if (Schema::hasColumn('categories', $col)) {
                        $columnsToDrop[] = $col;
                    }
                }
                if (!empty($columnsToDrop)) {
                    if (in_array('branch_id', $columnsToDrop)) {
                        try {
                            $table->dropForeign(['branch_id']);
                        } catch (\Exception $e) {
                            // ignore if constraint doesn't exist
                        }
                    }
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
