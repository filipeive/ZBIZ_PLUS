<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('roles')->where('name', 'super_admin')->doesntExist()) {
            DB::table('roles')->insert([
                'name' => 'super_admin',
                'guard_name' => 'web',
                'description' => 'Acesso total ao sistema, gerencia administradores',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('name', 'super_admin')->delete();
    }
};
