<?php

namespace Tests\Feature\Settings;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_and_superadmin_can_update_settings_without_duplicate_key_error(): void
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // 1. Criar dois tenants distintos
        $tenant1 = Tenant::create([
            'id'            => 1,
            'name'          => 'Farmácia 1',
            'slug'          => 'farmacia-1',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        $tenant2 = Tenant::create([
            'id'            => 2,
            'name'          => 'Farmácia 2',
            'slug'          => 'farmacia-2',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'name'      => 'Admin Farmácia 1',
            'email'     => 'admin1@test.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $roleAdmin->id,
        ]);

        $user2 = User::create([
            'tenant_id' => $tenant2->id,
            'name'      => 'Admin Farmácia 2',
            'email'     => 'admin2@test.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $roleAdmin->id,
        ]);

        // Tenant 1 salva configurações
        app(TenantContext::class)->setTenant($tenant1);
        $response1 = $this->actingAs($user1)->post('/settings', [
            'company_name'  => 'Farmácia 1 Atualizada',
            'business_type' => 'pharmacy',
            'company_nuit'  => '123456789',
            'tax_rate'      => 16,
        ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('success');

        // Tenant 2 salva configurações com a mesma chave 'company_name' sem conflito
        app(TenantContext::class)->setTenant($tenant2);
        $response2 = $this->actingAs($user2)->post('/settings', [
            'company_name'  => 'Farmácia 2 Atualizada',
            'business_type' => 'pharmacy',
            'company_nuit'  => '987654321',
            'tax_rate'      => 16,
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('success');

        // Verificar que ambos foram persistidos corretamente na tabela settings
        $this->assertDatabaseHas('settings', [
            'tenant_id' => 1,
            'key'       => 'company_name',
            'value'     => 'Farmácia 1 Atualizada',
        ]);

        $this->assertDatabaseHas('settings', [
            'tenant_id' => 2,
            'key'       => 'company_name',
            'value'     => 'Farmácia 2 Atualizada',
        ]);
    }
}

