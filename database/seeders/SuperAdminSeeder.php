<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds to guarantee the platform Owner / Super Admin exists.
     */
    public function run(): void
    {
        // 1. Assegurar que os perfis de acesso existem
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['description' => 'Super Administrador da Plataforma / Owner']
        );

        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Administrador da Empresa / Gerente Geral']
        );

        Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            ['description' => 'Gerente de Loja / Farmacêutico Responsável']
        );

        Role::firstOrCreate(
            ['name' => 'cashier', 'guard_name' => 'web'],
            ['description' => 'Operador de Caixa / Balcão de Venda']
        );

        // 2. Definir credenciais do SuperAdmin / Owner
        $name     = env('SUPERADMIN_NAME', 'Super Admin ZBPOS+');
        $email    = env('SUPERADMIN_EMAIL', 'superadmin@zbizpos.com');
        $password = env('SUPERADMIN_PASSWORD', 'password123');
        $phone    = env('SUPERADMIN_PHONE', '862134230');

        // 3. Em ambientes locais/offline, se existir um tenant primário, vincular para conveniência
        $tenantId = null;
        if (config('app.installation_mode') === 'offline' || env('INSTALLATION_MODE') === 'offline') {
            $firstTenant = Tenant::first();
            if ($firstTenant) {
                $tenantId = $firstTenant->id;
            }
        }

        // 4. Criar ou atualizar o SuperAdmin de forma idempotente
        $superAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name'          => $name,
                'employee_code' => 'SA-001',
                'phone'         => $phone,
                'password'      => Hash::make($password),
                'role_id'       => $superAdminRole->id,
                'tenant_id'     => $tenantId,
                'is_active'     => true,
            ]
        );

        // 5. Sincronizar Spatie Role se aplicável
        if (method_exists($superAdmin, 'syncRoles')) {
            try {
                $superAdmin->syncRoles(['super_admin']);
            } catch (\Throwable $e) {
                // Silencioso caso a tabela intermediária de permissões não esteja ativa
            }
        }

        $this->command?->info("=================================================================");
        $this->command?->info("✅ SUPERADMIN / OWNER CONFIGURADO COM SUCESSO!");
        $this->command?->info("   Nome:          {$name}");
        $this->command?->info("   E-mail:        {$email}");
        $this->command?->info("   Telemóvel:     {$phone}");
        $this->command?->info("   Palavra-passe: {$password}");
        $this->command?->info("   Perfil:        super_admin");
        $this->command?->info("=================================================================");
    }
}

