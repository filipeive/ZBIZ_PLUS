<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
        $this->middleware('guest')->except('showSuccess');
    }

    /**
     * Exibir formulário de Pré-Registo e Criação de Empresa SaaS.
     */
    public function showRegistrationForm(Request $request)
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $selectedPlan = $request->query('plan', 'starter');
        $selectedSector = $request->query('sector', 'retail');

        return view('auth.register', compact('plans', 'selectedPlan', 'selectedSector'));
    }

    /**
     * Processar o Pré-Registo do Tenant e enviar credenciais oficiais por SMS e E-mail.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name'   => 'required|string|max:150',
            'business_type'  => 'required|string|in:retail,pharmacy,reprography,restaurant,services,other',
            'nuit'           => 'nullable|string|max:15',
            'province'       => 'required|string|max:50',
            'city'           => 'nullable|string|max:100',
            'branch_name'    => 'nullable|string|max:100',
            'admin_name'     => 'required|string|max:120',
            'email'          => 'required|string|email|max:150|unique:users,email',
            'phone'          => 'required|string|max:30',
            'password'       => 'required|string|min:6|confirmed',
            'plan_slug'      => 'required|string|exists:plans,slug',
        ], [
            'company_name.required'  => 'O nome da empresa é obrigatório.',
            'business_type.required' => 'Selecione o ramo de atividade da sua empresa.',
            'admin_name.required'    => 'O nome do responsável/administrador é obrigatório.',
            'email.required'         => 'O e-mail para acesso é obrigatório.',
            'email.unique'           => 'Este e-mail já está registado no sistema.',
            'phone.required'         => 'O número de telemóvel para receber o SMS é obrigatório.',
            'password.required'      => 'Defina uma senha de acesso.',
            'password.min'           => 'A senha deve conter pelo menos 6 caracteres.',
            'password.confirmed'     => 'A confirmação da senha não confere.',
        ]);

        $createdData = DB::transaction(function () use ($validated) {
            // 1. Criar o Tenant
            $slug = Str::slug($validated['company_name']);
            if (Tenant::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::lower(Str::random(4));
            }

            $tenant = Tenant::create([
                'name'                 => $validated['company_name'],
                'slug'                 => $slug,
                'subdomain'            => $slug,
                'business_type'        => $validated['business_type'],
                'nuit'                 => $validated['nuit'] ?? null,
                'email'                => $validated['email'],
                'phone'                => $validated['phone'],
                'address'              => ($validated['city'] ?? '') . ', ' . $validated['province'],
                'currency'             => 'MZN',
                'status'               => 'pending', // Aguarda aprovação do dono
                'installation_mode'    => 'cloud',
                'license_status'       => 'pending',
                'trial_ends_at'        => null,
                'subscription_ends_at' => null,
            ]);

            // 2. Criar a Filial Principal (Sede)
            $branchName = !empty($validated['branch_name']) ? $validated['branch_name'] : 'Loja Principal';
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'name'      => $branchName,
                'code'      => 'SEDE',
                'phone'     => $validated['phone'],
                'address'   => ($validated['city'] ?? '') . ', ' . $validated['province'],
                'is_main'   => true,
                'is_active' => true,
            ]);

            // 3. Criar Contas Financeiras Padrão
            FinancialAccount::create([
                'tenant_id'       => $tenant->id,
                'branch_id'       => $branch->id,
                'name'            => 'Caixa Principal',
                'slug'            => 'caixa-principal',
                'type'            => 'cash',
                'current_balance' => 0,
                'is_active'       => true,
                'sort_order'      => 1,
            ]);

            FinancialAccount::create([
                'tenant_id'       => $tenant->id,
                'branch_id'       => $branch->id,
                'name'            => 'Carteira Móvel M-Pesa / e-Mola',
                'slug'            => 'carteira-movel',
                'type'            => 'mobile_money',
                'current_balance' => 0,
                'is_active'       => true,
                'sort_order'      => 2,
            ]);

            // 4. Criar Categorias Especializadas por Setor
            \App\Services\Tenant\TenantSectorService::seedCategoriesForTenant($tenant, $validated['business_type']);

            // 5. Se for Restaurante, criar mesas operacionais iniciais
            if ($validated['business_type'] === 'restaurant') {
                RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Mesa 01', 'capacity' => 4, 'status' => 'available']);
                RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Mesa 02', 'capacity' => 4, 'status' => 'available']);
                RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Mesa 03', 'capacity' => 6, 'status' => 'available']);
                RestaurantTable::create(['tenant_id' => $tenant->id, 'branch_id' => $branch->id, 'name' => 'Balcão / Bar', 'capacity' => 2, 'status' => 'available']);
            }

            // 6. Obter Role de Administrador do Tenant
            $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'], ['description' => 'Administrador da Empresa']);

            // 7. Criar o Usuário Administrador (inativo até aprovação do dono)
            $user = User::create([
                'tenant_id'         => $tenant->id,
                'branch_id'         => $branch->id,
                'name'              => $validated['admin_name'],
                'email'             => $validated['email'],
                'phone'             => $validated['phone'],
                'password'          => Hash::make($validated['password']),
                'role_id'           => $role->id,
                'is_active'         => false, // Ativado apenas após aprovação
                'email_verified_at' => now(),
            ]);

            // 8. Registar Subscrição Inicial Pendente
            $plan = Plan::where('slug', $validated['plan_slug'])->first() ?? Plan::first();
            if ($plan) {
                \App\Models\Subscription::create([
                    'tenant_id'              => $tenant->id,
                    'plan_id'                => $plan->id,
                    'status'                 => 'pending',
                    'payment_method'         => 'manual',
                    'last_payment_reference' => 'PRE-REGISTO',
                ]);
            }

            return compact('tenant', 'user', 'plan');
        });

        $user = $createdData['user'];
        $tenant = $createdData['tenant'];
        $plan = $createdData['plan'];

        // 9. Notificar a equipe Fdsmultiservices sobre novo pré-registo a aguardar aprovação
        try {
            SmsService::sendSms(
                '+258862134230',
                "ZBIZ+ | Novo Pre-Registo!\n"
                . "Empresa: {$tenant->name}\n"
                . "Gestor: {$user->name} ({$user->phone})\n"
                . "Plano: {$plan->name}\n"
                . "Aceda ao Painel do Dono para aprovar o teste.",
                $tenant->id
            );
        } catch (\Throwable $e) {
            // Log silenciado para não interromper o fluxo do utilizador
        }

        // 10. Gravar dados na sessão para a tela de confirmação (NÃO LOGAR O USUÁRIO)
        session([
            'reg_pending'      => true,
            'reg_company_name' => $tenant->name,
            'reg_admin_name'   => $user->name,
            'reg_email'        => $user->email,
            'reg_phone'        => SmsService::normalizePhone($validated['phone']) ?? $validated['phone'],
            'reg_plan_name'    => $plan?->name ?? 'ZBIZ Starter',
        ]);

        return redirect()->route('register.success');
    }

    /**
     * Exibir tela de confirmação de Pré-Registo Submetido.
     */
    public function showSuccess()
    {
        if (!session('reg_pending')) {
            return redirect()->route('login');
        }

        return view('auth.register_success');
    }
}
