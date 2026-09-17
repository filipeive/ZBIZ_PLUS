<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with structural production essentials.
     * ZERO test data, ZERO test users, ZERO test sales.
     */
    public function run(): void
    {
        // 1. Planos de Subscrição do ZBIZ+ e Super Administrador (Owner)
        $this->call([
            PlanSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // 2. Perfis de Acesso Essenciais do Sistema
        $roles = [
            ['name' => 'super_admin', 'description' => 'Super Administrador da Plataforma'],
            ['name' => 'admin',       'description' => 'Administrador da Empresa / Gerente Geral'],
            ['name' => 'manager',     'description' => 'Gerente de Loja / Farmacêutico Responsável'],
            ['name' => 'cashier',     'description' => 'Operador de Caixa / Balcão de Venda'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                ['description' => $roleData['description']]
            );
        }
    }
}
