<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenant\TenantSectorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateClientTenant extends Command
{
    /**
     * Assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'zbiz:new-tenant
                            {--name= : Nome comercial da Empresa / Farmácia}
                            {--type=pharmacy : Ramo de atividade (pharmacy, retail, restaurant, reprography, services)}
                            {--admin-name= : Nome do Administrador / Responsável}
                            {--email= : E-mail de login para acesso}
                            {--password= : Senha de acesso (mínimo 6 caracteres)}
                            {--phone= : Telemóvel do responsável}
                            {--nuit= : NUIT da empresa}
                            {--province=Maputo Cidade : Província}
                            {--city=Maputo : Cidade / Distrito}
                            {--plan=pharmacy_plus : Slug do plano (pharmacy_plus, starter, pro, business, enterprise)}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Cria uma instalação limpa de cliente (ex: Farmácia) sem dados de teste, 100% pronta para produção';

    /**
     * Execução do comando.
     */
    public function handle(): int
    {
        $this->info('====================================================================');
        $this->info('   ZBIZ+ Enterprise Suite - Criador de Instalação Limpa de Cliente');
        $this->info('====================================================================');
        $this->newLine();

        // 1. Coleta de dados com interatividade
        $name = $this->option('name') ?: $this->ask('Nome comercial da Empresa / Farmácia', 'Farmácia Exemplo');
        $type = $this->option('type') ?: $this->choice(
            'Ramo de atividade',
            ['pharmacy', 'retail', 'restaurant', 'reprography', 'services'],
            0
        );

        $nuit = $this->option('nuit') ?: $this->ask('NUIT da empresa (opcional)', '');
        $province = $this->option('province') ?: $this->ask('Província', 'Maputo Cidade');
        $city = $this->option('city') ?: $this->ask('Cidade / Distrito', 'Maputo');
        $adminName = $this->option('admin-name') ?: $this->ask('Nome completo do Administrador / Farmacêutico', 'Dr. Responsável');
        
        $email = $this->option('email');
        while (empty($email) || User::where('email', $email)->exists()) {
            if (!empty($email)) {
                $this->error("O e-mail [{$email}] já está registado no sistema. Por favor informe outro.");
            }
            $email = $this->ask('E-mail de acesso do Administrador', 'admin@' . Str::slug($name) . '.co.mz');
        }

        $password = $this->option('password') ?: $this->secret('Senha de acesso (mínimo 6 caracteres)') ?: '12345678';
        $phone = $this->option('phone') ?: $this->ask('Telemóvel do responsável (ex: +258 84 000 0000)', '+258 84 000 0000');
        
        $planSlug = $this->option('plan') ?: 'pharmacy_plus';
        $plan = Plan::where('slug', $planSlug)->first() ?: Plan::first();

        $this->newLine();
        $this->info("⏳ A inicializar instalação limpa para [{$name}] ({$type})...");

        // 2. Transação segura de criação
        try {
            DB::transaction(function () use ($name, $type, $nuit, $province, $city, $adminName, $email, $password, $phone, $plan) {
                // Slug do Tenant
                $slug = Str::slug($name);
                if (Tenant::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::lower(Str::random(4));
                }

                // 2.1 Criar Tenant Ativo
                $tenant = Tenant::create([
                    'name'                 => $name,
                    'slug'                 => $slug,
                    'subdomain'            => $slug,
                    'business_type'        => $type,
                    'nuit'                 => !empty($nuit) ? $nuit : null,
                    'email'                => $email,
                    'phone'                => $phone,
                    'address'              => "{$city}, {$province}",
                    'currency'             => 'MZN',
                    'status'               => 'active',
                    'installation_mode'    => 'local',
                    'license_status'       => 'active',
                    'trial_ends_at'        => null,
                    'subscription_ends_at' => now()->addYear(),
                ]);

                // 2.2 Criar Filial Sede
                $branch = Branch::create([
                    'tenant_id' => $tenant->id,
                    'name'      => 'Loja / Sede Principal',
                    'code'      => 'SEDE',
                    'phone'     => $phone,
                    'address'   => "{$city}, {$province}",
                    'is_main'   => true,
                    'is_active' => true,
                ]);

                // 2.3 Criar Contas Financeiras Padrão (Zeradas)
                FinancialAccount::create([
                    'tenant_id'       => $tenant->id,
                    'branch_id'       => $branch->id,
                    'name'            => 'Caixa Principal',
                    'slug'            => 'caixa-principal',
                    'type'            => 'cash',
                    'current_balance' => 0.00,
                    'is_active'       => true,
                    'sort_order'      => 1,
                ]);

                FinancialAccount::create([
                    'tenant_id'       => $tenant->id,
                    'branch_id'       => $branch->id,
                    'name'            => 'Carteira Móvel M-Pesa / e-Mola',
                    'slug'            => 'carteira-movel',
                    'type'            => 'mobile_money',
                    'current_balance' => 0.00,
                    'is_active'       => true,
                    'sort_order'      => 2,
                ]);

                // 2.4 Semear Categorias Especializadas por Setor
                TenantSectorService::seedCategoriesForTenant($tenant, $type);

                // 2.5 Se for Restaurante, criar mesas iniciais
                if ($type === 'restaurant') {
                    RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Mesa 01', 'capacity' => 4, 'status' => 'available']);
                    RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Mesa 02', 'capacity' => 4, 'status' => 'available']);
                    RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Balcão / Bar', 'capacity' => 2, 'status' => 'available']);
                }

                // 2.6 Role de Administrador
                $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['description' => 'Administrador da Empresa']);

                // 2.7 Utilizador Administrador Ativo
                User::create([
                    'tenant_id'         => $tenant->id,
                    'branch_id'         => $branch->id,
                    'name'              => $adminName,
                    'email'             => $email,
                    'phone'             => $phone,
                    'password'          => Hash::make($password),
                    'role_id'           => $role->id,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]);

                // 2.8 Subscrição Ativa de 1 Ano
                if ($plan) {
                    Subscription::create([
                        'tenant_id'              => $tenant->id,
                        'plan_id'                => $plan->id,
                        'status'                 => 'active',
                        'starts_at'              => now(),
                        'ends_at'                => now()->addYear(),
                        'payment_method'         => 'license_activation',
                        'last_payment_reference' => 'ONBOARDING-OFICIAL',
                    ]);
                }
            });

            $this->newLine();
            $this->info('====================================================================');
            $this->info('✅ INSTALAÇÃO LIMPA CONCLUÍDA COM SUCESSO!');
            $this->info('====================================================================');
            $this->table(
                ['Campo', 'Valor Configurado'],
                [
                    ['Empresa / Farmácia', $name],
                    ['Ramo de Atividade', strtoupper($type)],
                    ['NUIT', $nuit ?: 'Não atribuído'],
                    ['Localização', "{$city}, {$province}"],
                    ['Administrador', $adminName],
                    ['E-mail de Login', $email],
                    ['Plano Atribuído', $plan ? $plan->name : 'N/A'],
                    ['Validade da Licença', now()->addYear()->format('d/m/Y')],
                    ['Estado de Dados', '100% LIMPO (Zero vendas, zero produtos de teste)'],
                ]
            );
            $this->newLine();
            $this->comment('Dica: Inicie o servidor e aceda à tela de login para começar a cadastrar produtos reais.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Falha na instalação: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
