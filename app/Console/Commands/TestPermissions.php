<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\PermissionHelper;

class TestPermissions extends Command
{
    protected $signature = 'test:permissions {email?}';
    protected $description = 'Testar sistema de permissões';

    public function handle()
    {
        $email = $this->argument('email') ?? 'admin@shop.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário {$email} não encontrado!");
            return;
        }

        auth()->login($user);

        $this->info("=== TESTE DE PERMISSÕES ===");
        $this->info("Usuário: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Role: {$user->role}");
        $this->info("Ativo: " . ($user->is_active ? 'SIM' : 'NÃO'));
        $this->info("---");

        // Testar funções básicas
        $this->info("isAdmin(): " . (PermissionHelper::isAdmin() ? 'SIM' : 'NÃO'));
        $this->info("isManager(): " . (PermissionHelper::isManager() ? 'SIM' : 'NÃO'));
        $this->info("isStaff(): " . (PermissionHelper::isStaff() ? 'SIM' : 'NÃO'));
        $this->info("getUserLevel(): " . PermissionHelper::getUserLevel());
        $this->info("---");

        // Testar algumas permissões
        $permissionsToTest = [
            'manage_categories',
            'view_reports',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_users'
        ];

        foreach ($permissionsToTest as $permission) {
            $result = PermissionHelper::userCan($permission) ? 'SIM' : 'NÃO';
            $this->info("userCan('{$permission}'): {$result}");
        }

        $this->info("=== FIM DO TESTE ===");
    }
}