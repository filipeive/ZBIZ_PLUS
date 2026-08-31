<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
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
        $this->middleware('guest');
    }

    /**
     * Exibir formulário de Onboarding e Criação de Empresa SaaS.
     */
    public function showRegistrationForm(Request $request)
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $selectedPlan = $request->query('plan', 'starter');
        $selectedSector = $request->query('sector', 'retail');

        return view('auth.register', compact('plans', 'selectedPlan', 'selectedSector'));
    }

    /**
     * Processar o Onboarding Completo do Tenant e criar Usuário Administrador.
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
            'admin_name'     => 'nullable|string|max:120',
            'name'           => 'nullable|string|max:120',
            'email'          => 'required|string|email|max:150|unique:users,email',
            'phone'          => 'required|string|max:30',
            'password'       => 'required|string|min:6|confirmed',
            'plan_slug'      => 'required|string|exists:plans,slug',
        ], [
            'company_name.required'  => 'O nome da empresa é obrigatório.',
            'business_type.required' => 'Selecione o setor de atividade da sua empresa.',
            'admin_name.required'    => 'O nome do administrador é obrigatório.',
            'email.required'         => 'O e-mail é obrigatório.',
            'email.unique'           => 'Este e-mail já está cadastrado no sistema.',
            'password.required'      => 'Defina uma senha de acesso.',
            'password.min'           => 'A senha deve conter pelo menos 6 caracteres.',
            'password.confirmed'     => 'A confirmação da senha não confere.',
        ]);

        $user = DB::transaction(function () use ($validated) {
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

            // 2. Criar a Filial Principal
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

            // 4. Criar Categorias Padrão especializadas por Setor
            \App\Services\Tenant\TenantSectorService::seedCategoriesForTenant($tenant, $validated['business_type']);

            // 5. Obter ou Criar Role Super Admin
            $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web'], ['description' => 'Administrador Geral']);

            // 6. Criar o Usuário Administrador
            $user = User::create([
                'tenant_id'         => $tenant->id,
                'branch_id'         => $branch->id,
                'name'              => $validated['admin_name'] ?? $validated['name'] ?? 'Administrador',
                'email'             => $validated['email'],
                'phone'             => $validated['phone'],
                'password'          => Hash::make($validated['password']),
                'role_id'           => $role->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            // 7. Ativar Plano com 30 Dias de Avaliação Gratuita (Trial)
            $plan = Plan::where('slug', $validated['plan_slug'])->first() ?? Plan::first();
            if ($plan) {
                $this->subscriptionService->startTrial($tenant, $plan, 30);
            }

            return $user;
        });

        // 8. Autenticar automaticamente e redirecionar
        Auth::login($user);

        return redirect()->route('dashboard.index')->with('success', 
            "Bem-vindo ao ZBIZ+! A sua empresa foi criada com sucesso com 30 dias de avaliação gratuita no plano {$validated['plan_slug']}."
        );
    }
}
