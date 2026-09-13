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
                'name'          => $validated['company_name'],
                'slug'          => $slug,
                'subdomain'     => $slug,
                'business_type' => $validated['business_type'],
                'nuit'          => $validated['nuit'] ?? null,
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'address'       => ($validated['city'] ?? '') . ', ' . $validated['province'],
                'currency'      => 'MZN',
                'status'        => 'trial',
                'trial_ends_at' => now()->addDays(30),
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

            // 7. Criar o Usuário Administrador
            $user = User::create([
                'tenant_id'         => $tenant->id,
                'branch_id'         => $branch->id,
                'name'              => $validated['admin_name'],
                'email'             => $validated['email'],
                'phone'             => $validated['phone'],
                'password'          => Hash::make($validated['password']),
                'role_id'           => $role->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            // 8. Ativar Plano com 30 Dias de Avaliação no Plano Selecionado
            $plan = Plan::where('slug', $validated['plan_slug'])->first() ?? Plan::first();
            if ($plan) {
                $this->subscriptionService->startTrial($tenant, $plan, 30);
            }

            return compact('tenant', 'user', 'plan');
        });

        $user = $createdData['user'];
        $tenant = $createdData['tenant'];
        $plan = $createdData['plan'];

        // 9. Enviar Credenciais de Acesso por SMS para o Telemóvel do Cliente
        [$smsSent, $smsFeedback] = SmsService::sendCredentialsSms(
            $validated['phone'],
            $user->name,
            $tenant->name,
            $user->email,
            $validated['password']
        );

        // 10. Iniciar sessão do utilizador
        Auth::login($user);

        // 11. Gravar dados na sessão para a tela de confirmação
        session([
            'reg_success'      => true,
            'reg_company_name' => $tenant->name,
            'reg_admin_name'   => $user->name,
            'reg_email'        => $user->email,
            'reg_password'     => $validated['password'],
            'reg_phone'        => SmsService::normalizePhone($validated['phone']) ?? $validated['phone'],
            'reg_plan_name'    => $plan?->name ?? 'ZBIZ Starter',
            'reg_sms_sent'     => $smsSent,
            'reg_sms_feedback' => $smsFeedback,
        ]);

        return redirect()->route('register.success');
    }

    /**
     * Exibir tela de confirmação de Pré-Registo e envio de SMS.
     */
    public function showSuccess()
    {
        if (!session('reg_success')) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.register_success');
    }
}
