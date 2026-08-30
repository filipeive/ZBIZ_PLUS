<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Certificar que o papel de admin existe
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'admin',
                'description' => 'Administrador do Sistema'
            ]);
        }

        // Criar usuário demo
        User::updateOrCreate(
            ['email' => 'demo@reprosys.com'],
            [
                'name' => 'Visitante Demonstrativo',
                'password' => Hash::make('demo123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );
    }
}
