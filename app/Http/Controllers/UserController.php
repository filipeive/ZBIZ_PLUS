<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\FinancialAccount;
use App\Models\SalaryPayment;
use App\Models\UserActivity;
use App\Models\TemporaryPassword;
use App\Services\FinancialService;
use App\Traits\LogsActivity; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use LogsActivity; 

    public function index(Request $request)
    {
        $isEmployeesView = $request->routeIs('users.employees');
        $query = User::with(['role', 'activeTemporaryPasswords'])->orderBy('name');

        if ($isEmployeesView) {
            $query->whereHas('role', fn ($q) => $q->whereIn('name', ['staff', 'manager']));
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role') && !$isEmployeesView) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Aplicar ordenação
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at', 'last_login_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admin' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'manager' => User::whereHas('role', fn($q) => $q->where('name', 'manager'))->count(),
            'staff' => User::whereHas('role', fn($q) => $q->where('name', 'staff'))->count(),
            'with_temp_password' => User::whereHas('activeTemporaryPasswords')->count(),
        ];

        return view('users.index', compact('users', 'stats', 'isEmployeesView'));
    }

    public function create()
    {
        $roles = Role::all();
        $defaultRole = request('role');
        return view('users.create', compact('roles', 'defaultRole'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'employee_code' => 'nullable|string|max:50|unique:users,employee_code',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'monthly_salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'job_title' => 'nullable|string|max:100',
            'document_number' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data = $request->except('photo', 'password_confirmation');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        $data['password'] = Hash::make($request->password);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        // Registrar log
        $this->logActivity('create', "Criou novo usuário: {$user->name}", $user);

        return $this->success('users.index', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        $user->load(['role', 'activities' => function($query) {
            $query->latest()->limit(5);
        }, 'temporaryPasswords' => function($query) {
            $query->latest()->limit(3);
        }, 'salaryPayments' => function ($query) {
            $query->with(['account', 'payer'])->latest('payment_date')->limit(10);
        }]);

        $financialAccounts = FinancialAccount::operational()
            ->orderBy('sort_order')
            ->get();

        return view('users.show', compact('user', 'financialAccounts'));
    }

    public function edit(User $user)
    {
        //$this->authorize('canEdit', $user);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        //$this->authorize('canEdit', $user);

        $rules = [
            'name' => 'required|string|max:255',
            'employee_code' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users', 'employee_code')->ignore($user->id)],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'monthly_salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'job_title' => 'nullable|string|max:100',
            'document_number' => 'nullable|string|max:50',
            'role_id' => 'required|exists:roles,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $oldName = $user->name;
        $data = $request->except('photo', 'password_confirmation');

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            
            // Invalidar senhas temporárias quando senha for alterada manualmente
            $user->activeTemporaryPasswords()->update([
                'used' => true,
                'used_at' => now()
            ]);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        // Registrar log
        $this->logActivity('update', "Atualizou usuário de '{$oldName}' para '{$user->name}'", $user);

        return $this->success('users.index', 'Usuário atualizado com sucesso!');
    }

    public function storeSalaryPayment(Request $request, User $user, FinancialService $financialService)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'base_amount' => 'required|numeric|min:0',
            'variable_amount' => 'nullable|numeric|min:-1500|max:1500',
            'payment_date' => 'required|date',
            'reference_month' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $baseAmount = (float) $validated['base_amount'];
        $variableAmount = (float) ($validated['variable_amount'] ?? 0);
        $totalAmount = $baseAmount + $variableAmount;

        $description = "Pagamento de salário - {$user->name}";
        if ($variableAmount > 0) {
            $description .= " (Base: " . number_format($baseAmount, 2) . " + Variável: " . number_format($variableAmount, 2) . ")";
        }

        $salaryPayment = $financialService->createSalaryPayment([
            'user_id' => $user->id,
            'financial_account_id' => $validated['financial_account_id'],
            'paid_by' => auth()->id(),
            'amount' => $totalAmount,
            'base_amount' => $baseAmount,
            'variable_amount' => $variableAmount,
            'payment_date' => $validated['payment_date'],
            'reference_month' => $validated['reference_month'] ?? null,
            'description' => $description,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->logActivity('salary_payment', "Registrou pagamento salarial para {$user->name} (MT " . number_format($totalAmount, 2) . ")", $user);

        return redirect()->route('users.show', $user)->with('success', 'Pagamento salarial registrado com sucesso.');
    }

    /**
     * Upload signed receipt for a salary payment.
     */
    public function uploadSalaryReceipt(Request $request, User $user, SalaryPayment $payment)
    {
        $request->validate([
            'signed_receipt' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        // Remover recibo anterior se existir
        if ($payment->signed_receipt_path) {
            Storage::disk('public')->delete($payment->signed_receipt_path);
        }

        $path = $request->file('signed_receipt')->store('salary-receipts/' . $user->id, 'public');
        $payment->update(['signed_receipt_path' => $path]);

        $this->logActivity('receipt_upload', "Carregou recibo assinado do pagamento salarial #{$payment->id} de {$user->name}", $user);

        return redirect()->route('users.show', $user)->with('success', 'Recibo assinado carregado com sucesso.');
    }

    /**
     * Generate printable salary receipt.
     */
    public function salaryReceipt(User $user, SalaryPayment $payment)
    {
        $pdf = Pdf::loadView('users.salary-receipt', compact('user', 'payment'));
        
        $filename = 'recibo-salario-' . $user->id . '-' . ($payment->reference_month ? $payment->reference_month->format('m-Y') : $payment->payment_date->format('m-Y')) . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function payroll(Request $request)
    {
        $referenceMonth = Carbon::parse($request->input('reference_month', now()->startOfMonth()->format('Y-m-d')))
            ->startOfMonth();

        $employees = User::with(['role'])
            ->whereHas('role', fn ($query) => $query->where('name', 'staff'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $paymentsByUser = SalaryPayment::whereMonth('reference_month', $referenceMonth->month)
            ->whereYear('reference_month', $referenceMonth->year)
            ->get()
            ->groupBy('user_id');

        $payrollRows = $employees->map(function (User $employee) use ($paymentsByUser, $referenceMonth) {
            $baseSalary = (float) ($employee->monthly_salary ?? 0);
            $payments = $paymentsByUser->get($employee->id, collect());
            $paidAmount = (float) $payments->sum('amount');
            $balance = $baseSalary - $paidAmount;

            return [
                'employee' => $employee,
                'reference_month' => $referenceMonth,
                'base_salary' => $baseSalary,
                'paid_amount' => $paidAmount,
                'balance' => $balance,
                'status' => $baseSalary <= 0
                    ? 'undefined'
                    : ($balance <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending')),
                'payments' => $payments, // Adicionado os pagamentos para mostrar recibos
            ];
        });

        $summary = [
            'employees' => $payrollRows->count(),
            'base_total' => (float) $payrollRows->sum('base_salary'),
            'paid_total' => (float) $payrollRows->sum('paid_amount'),
            'balance_total' => (float) $payrollRows->sum('balance'),
        ];

        $financialAccounts = FinancialAccount::operational()->orderBy('sort_order')->get();

        return view('users.payroll', compact('referenceMonth', 'payrollRows', 'summary', 'financialAccounts'));
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('canDelete', $user);

        $userName = $user->name;

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->delete();

        // Registrar log
        $this->logActivity('delete', "Excluiu usuário: {$userName}", $user);

        return $this->success('users.index', 'Usuário excluído com sucesso!');
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para alterar o status deste usuário.'
            ], 403);
        }

        $currentStatus = $user->is_active;
        $user->update(['is_active' => !$currentStatus]);

        // Se desativado, invalidar senhas temporárias
        if (!$currentStatus === false) {
            $user->activeTemporaryPasswords()->update([
                'used' => true,
                'used_at' => now()
            ]);
        }

        // Registrar log
        $this->logActivity('status_change', "Alterou status do usuário '{$user->name}' para " . (!$currentStatus ? 'ativo' : 'inativo'), $user);

        return response()->json([
            'success' => true,
            'message' => "Usuário {$user->name} " . (!$currentStatus ? 'ativado' : 'desativado') . " com sucesso!",
            'new_status' => !$currentStatus
        ]);
    }

    /**
     * Display user activity log.
     */
    public function activity(User $user)
    {
        if (!auth()->user()->canView($user)) {
            return $this->error('users.index', 'Você não tem permissão para visualizar a atividade deste usuário.');
        }

        $activities = $user->activities()->paginate(20);

        return view('users.activity', compact('user', 'activities'));
    }
    
    /**
     * Reset user password with temporary password system.
     */
    public function resetPassword(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para resetar a senha deste usuário.'
            ], 403);
        }

        try {
            // Gerar senha temporária
            $temporaryPassword = $this->generateSecurePassword();
            
            // Criar registro de senha temporária
            $tempPassword = TemporaryPassword::createForUser($user, $temporaryPassword, 24);
            
            // Atualizar senha do usuário com a temporária
            $user->update([
                'password' => Hash::make($temporaryPassword),
            ]);

            // Registrar log
            $this->logActivity('password_reset', "Resetou senha do usuário: {$user->name} (Expira em 24h)", $user);

            return response()->json([
                'success' => true,
                'password' => $temporaryPassword,
                'expires_at' => $tempPassword->expires_at->format('d/m/Y H:i'),
                'message' => 'Senha resetada com sucesso! A senha temporária expira em 24 horas.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor ao resetar a senha.'
            ], 500);
        }
    }

    /**
     * Show temporary passwords for a user.
     */
    public function temporaryPasswords(User $user)
    {
        if (!auth()->user()->canView($user)) {
            return $this->error('users.index', 'Você não tem permissão para visualizar as senhas temporárias deste usuário.');
        }

        $temporaryPasswords = $user->temporaryPasswords()->with('createdBy')->paginate(10);

        return view('users.temporary-passwords', compact('user', 'temporaryPasswords'));
    }

    /**
     * Invalidate all temporary passwords for a user.
     */
    public function invalidateTemporaryPasswords(User $user)
    {
        if (!auth()->user()->canEdit($user)) {
            return response()->json([
                'success' => false,
                'error' => 'Você não tem permissão para invalidar as senhas temporárias deste usuário.'
            ], 403);
        }

        $count = $user->activeTemporaryPasswords()->count();
        
        $user->activeTemporaryPasswords()->update([
            'used' => true,
            'used_at' => now()
        ]);

        // Registrar log
        $this->logActivity('password_invalidate', "Invalidou {$count} senha(s) temporária(s) do usuário: {$user->name}", $user);

        return response()->json([
            'success' => true,
            'message' => "Invalidadas {$count} senha(s) temporária(s) com sucesso!"
        ]);
    }

    /**
     * Generate a secure temporary password.
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        $password = '';
        
        // Garantir pelo menos um de cada tipo
        $password .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 1); // Minúscula
        $password .= substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1); // Maiúscula
        $password .= substr(str_shuffle('0123456789'), 0, 1); // Número
        $password .= substr(str_shuffle('!@#$%&*'), 0, 1); // Símbolo
        
        // Completar o resto
        for ($i = 4; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return str_shuffle($password);
    }
}
