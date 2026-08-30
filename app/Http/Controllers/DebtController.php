<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\DebtPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DebtController extends Controller
{
    public function __construct(private FinancialService $financialService) {}

    /**
     * Lista de dívidas
     */
    public function index(Request $request)
    {
        try {
            $query = Debt::query()->latest('created_at');

            // Filtros
            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('customer')) {
                $search = '%'.$request->customer.'%';
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', $search)
                        ->orWhere('employee_name', 'like', $search);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('debt_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('debt_date', '<=', $request->date_to);
            }

            $debts = $query->paginate(15)->withQueryString();
            $debts->load(['user', 'employee']);

            // Estatísticas
            $stats = [
                'total_active' => Debt::where('status', 'active')->sum('remaining_amount') ?? 0,
                'total_overdue' => Debt::where('status', 'active')
                    ->where('due_date', '<', now()->toDateString())
                    ->sum('remaining_amount') ?? 0,
                'count_active' => Debt::where('status', 'active')->count(),
                'count_paid_this_month' => Debt::where('status', 'paid')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
                'product_debts' => [
                    'total_active' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->sum('remaining_amount') ?? 0,
                    'count_active' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->count(),
                    'total_overdue' => Debt::where('debt_type', 'product')
                        ->where('status', 'active')
                        ->where('due_date', '<', now()->toDateString())
                        ->sum('remaining_amount') ?? 0,
                ],
                'money_debts' => [
                    'total_active' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->sum('remaining_amount') ?? 0,
                    'count_active' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->count(),
                    'total_overdue' => Debt::where('debt_type', 'money')
                        ->where('status', 'active')
                        ->where('due_date', '<', now()->toDateString())
                        ->sum('remaining_amount') ?? 0,
                ],
            ];

            return view('debts.index', compact('debts', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar dívidas: '.$e->getMessage());

            return back()->withErrors(['error' => 'Erro ao carregar dívidas']);
        }
    }

    /**
     * Mostrar formulário de criar
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'product');

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();

        return view('debts.create', compact('type', 'products', 'employees'));
    }

    /**
     * Salvar nova dívida
     */
    public function store(Request $request)
    {
        $rules = [
            'debt_type' => 'required|in:product,money',
            'debt_date' => 'required|date|before_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'initial_payment' => 'nullable|numeric|min:0',
        ];

        if ($request->debt_type === 'product') {
            $rules['customer_name'] = 'required|string|max:100';
            $rules['customer_phone'] = 'nullable|string|max:20';
            $rules['customer_document'] = 'nullable|string|max:20';
            $rules['products'] = 'required|string';
        } else {
            $rules['employee_id'] = 'nullable|exists:users,id';
            $rules['employee_name'] = 'required|string|max:100';
            $rules['employee_phone'] = 'nullable|string|max:20';
            $rules['employee_document'] = 'nullable|string|max:20';
            $rules['amount'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            if ($request->debt_type === 'product') {
                $debt = $this->createProductDebt($request);
            } else {
                $debt = $this->createMoneyDebt($request);
            }

            DB::commit();

            return redirect()->route('debts.show', $debt)
                ->with('success', 'Dívida criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar dívida: '.$e->getMessage());

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Criar dívida de produtos
     */
    private function createProductDebt(Request $request)
    {
        $products = json_decode($request->products, true);

        if (empty($products) || ! is_array($products) || count($products) === 0) {
            throw new \Exception('Adicione pelo menos um produto');
        }

        $totalAmount = 0;
        foreach ($products as $p) {
            $totalAmount += $p['quantity'] * $p['unit_price'];
        }

        $debt = Debt::create([
            'debt_type' => 'product',
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_document' => $request->customer_document,
            'original_amount' => $totalAmount,
            'remaining_amount' => $totalAmount,
            'debt_date' => $request->debt_date,
            'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
            'status' => 'active',
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        foreach ($products as $productData) {
            $product = Product::findOrFail($productData['product_id']);

            if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                throw new \Exception("Estoque insuficiente para {$product->name}");
            }

            DebtItem::create([
                'debt_id' => $debt->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total_price' => $productData['quantity'] * $productData['unit_price'],
            ]);

            if ($product->type === 'product') {
                $product->decrement('stock_quantity', $productData['quantity']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'movement_type' => 'out',
                    'quantity' => $productData['quantity'],
                    'reason' => "Dívida #$debt->id",
                    'reference_id' => $debt->id,
                    'movement_date' => $request->debt_date,
                ]);
            }
        }

        if ($request->filled('initial_payment') && $request->initial_payment > 0) {
            $this->processInitialPayment($debt, $request->initial_payment, $request->debt_date);
        }

        return $debt;
    }

    /**
     * Criar dívida de dinheiro
     */
    private function createMoneyDebt(Request $request)
    {
        $employeeId = $request->filled('employee_id') ? $request->employee_id : null;

        $debt = Debt::create([
            'debt_type' => 'money',
            'user_id' => auth()->id(),
            'employee_id' => $employeeId,
            'employee_name' => $request->employee_name,
            'employee_phone' => $request->employee_phone,
            'employee_document' => $request->employee_document,
            'original_amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'debt_date' => $request->debt_date,
            'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
            'status' => 'active',
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        if ($request->filled('initial_payment') && $request->initial_payment > 0) {
            $this->processInitialPayment($debt, $request->initial_payment, $request->debt_date);
        }

        $this->financialService->syncMoneyDebtDisbursement($debt);

        return $debt;
    }

    /**
     * Processar pagamento inicial
     */
    private function processInitialPayment(Debt $debt, float $amount, string $date)
    {
        $amount = min($amount, $debt->original_amount);

        $payment = DebtPayment::create([
            'debt_id' => $debt->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'payment_method' => 'cash',
            'payment_date' => $date,
            'notes' => 'Pagamento inicial',
        ]);

        $this->financialService->syncDebtPaymentTransaction($payment);

        $debt->remaining_amount -= $amount;
        if ($debt->remaining_amount <= 0) {
            $debt->status = 'paid';
            $debt->remaining_amount = 0;
        }
        $debt->save();
    }

    /**
     * Mostrar dívida
     */
    public function show(Debt $debt)
    {
        $debt->load(['user', 'employee', 'items.product', 'payments.user', 'generatedSale']);

        return view('debts.show', compact('debt'));
    }

    /**
     * Mostrar formulário de pagamento
     */
    public function payment(Debt $debt)
    {
        if (! $debt->canReceivePayment()) {
            return redirect()->route('debts.show', $debt)
                ->with('error', 'Esta dívida não pode receber pagamentos');
        }

        return view('debts.payment', compact('debt'));
    }

    /**
     * Registrar pagamento
     */
    public function addPayment(Request $request, Debt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$debt->remaining_amount,
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string',
            'create_sale' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            $payment = DebtPayment::create([
                'debt_id' => $debt->id,
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
            ]);

            $this->financialService->syncDebtPaymentTransaction($payment);

            $debt->remaining_amount -= $request->amount;

            if ($debt->remaining_amount <= 0.01) {
                $debt->status = 'paid';
                $debt->remaining_amount = 0;

                if ($debt->isProductDebt() && ! $debt->generated_sale_id && $request->has('create_sale')) {
                    $this->createSaleFromDebt($debt);
                }
            }

            $debt->save();

            // Garantir que a transação financeira seja criada se houver pagamento
            if (isset($payment)) {
                $this->financialService->syncDebtPaymentTransaction($payment);
            }

            DB::commit();

            return redirect()->route('debts.show', $debt)
                ->with('success', 'Pagamento registrado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar pagamento: '.$e->getMessage());

            return back()->withErrors(['error' => 'Erro ao processar pagamento']);
        }
    }

    /**
     * Criar venda a partir da dívida
     */
    private function createSaleFromDebt(Debt $debt)
    {
        $items = DebtItem::where('debt_id', $debt->id)->with('product')->get();

        if ($items->isEmpty()) {
            return null;
        }

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'customer_name' => $debt->customer_name,
            'customer_phone' => $debt->customer_phone,
            'subtotal' => $debt->original_amount,
            'total_amount' => $debt->original_amount,
            'payment_method' => 'debt_settlement',
            'sale_date' => now(),
            'notes' => "Venda da dívida #{$debt->id}",
        ]);

        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'original_unit_price' => $item->unit_price,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ]);
        }

        $debt->update(['generated_sale_id' => $sale->id]);

        return $sale;
    }

    /**
     * Cancelar dívida
     */
    public function cancel(Debt $debt)
    {
        if (! $debt->canBeCancelled()) {
            return back()->with('error', 'Esta dívida não pode ser cancelada');
        }

        try {
            DB::beginTransaction();

            if ($debt->isProductDebt()) {
                $items = DebtItem::where('debt_id', $debt->id)->with('product')->get();

                foreach ($items as $item) {
                    if ($item->product->type === 'product') {
                        $item->product->increment('stock_quantity', $item->quantity);

                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id' => auth()->id(),
                            'movement_type' => 'in',
                            'quantity' => $item->quantity,
                            'reason' => "Cancelamento dívida #$debt->id",
                            'reference_id' => $debt->id,
                            'movement_date' => now()->toDateString(),
                        ]);
                    }
                }
            }

            $debt->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('debts.index')
                ->with('success', 'Dívida cancelada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar: '.$e->getMessage());

            return back()->with('error', 'Erro ao cancelar dívida');
        }
    }

    /**
     * Relatório
     */
    public function debtorsReport(Request $request)
    {
        try {
            $query = Debt::whereNotIn('status', ['paid', 'cancelled']);

            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            $debtors = $query->get()->groupBy(function ($debt) {
                return $debt->isProductDebt() ? $debt->customer_name : $debt->employee_name;
            })->map(function ($debts) {
                $first = $debts->first();

                return (object) [
                    'debt_type' => $first->debt_type,
                    'debtor_name' => $first->isProductDebt() ? $first->customer_name : $first->employee_name,
                    'debtor_phone' => $first->isProductDebt() ? $first->customer_phone : $first->employee_phone,
                    'total_debt' => $debts->sum('remaining_amount'),
                    'debt_count' => $debts->count(),
                    'oldest_debt' => $debts->min('debt_date'),
                    'status_group' => $debts->contains(fn ($d) => $d->is_overdue) ? 'Vencida' : 'Ativa',
                ];
            })->sortByDesc('total_debt')->values();

            $page = $request->get('page', 1);
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $debtors = new \Illuminate\Pagination\LengthAwarePaginator(
                $debtors->slice($offset, $perPage)->values(),
                $debtors->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('debts.debtors-report', compact('debtors'));

        } catch (\Exception $e) {
            Log::error('Erro no relatório: '.$e->getMessage());

            return back()->with('error', 'Erro ao gerar relatório');
        }
    }

    /**
     * Mostrar detalhes (AJAX)
     */
    public function showDetails(Debt $debt)
    {
        try {
            $debt->load(['user', 'employee', 'items.product', 'payments.user', 'generatedSale']);
            $html = view('debts.partials.details', compact('debt'))->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao carregar detalhes'], 500);
        }
    }

    /**
     * Editar dívida
     */
    public function edit(Debt $debt)
    {
        if (! $debt->canBeEdited()) {
            return redirect()->route('debts.show', $debt)
                ->with('error', 'Esta dívida não pode ser editada');
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $employees = User::where('is_active', true)->orderBy('name')->get();

        return view('debts.edit', compact('debt', 'products', 'employees'));
    }

    /**
     * Atualizar dívida
     */
    public function update(Request $request, Debt $debt)
    {
        $rules = [
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];

        if ($debt->isProductDebt()) {
            $rules['customer_name'] = 'required|string|max:100';
            $rules['customer_phone'] = 'nullable|string|max:20';
            $rules['customer_document'] = 'nullable|string|max:20';
        } else {
            $rules['employee_name'] = 'required|string|max:100';
            $rules['employee_phone'] = 'nullable|string|max:20';
            $rules['employee_document'] = 'nullable|string|max:20';
        }

        $validated = $request->validate($rules);

        if (! $debt->canBeEdited()) {
            return back()->with('error', 'Esta dívida não pode ser editada');
        }

        try {
            $debt->update($validated);

            return redirect()->route('debts.show', $debt)
                ->with('success', 'Dívida atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar: '.$e->getMessage());

            return back()->withInput()->with('error', 'Erro ao atualizar dívida');
        }
    }

    /**
     * Marcar como paga
     */
    public function markAsPaid(Request $request, Debt $debt)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola',
            'create_sale' => 'sometimes|boolean',
        ]);

        if (! $debt->canBeMarkedAsPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta dívida não pode ser marcada como paga',
            ], 400);
        }

        try {
            DB::beginTransaction();

            if ($debt->remaining_amount > 0) {
                $payment = DebtPayment::create([
                    'debt_id' => $debt->id,
                    'user_id' => auth()->id(),
                    'amount' => $debt->remaining_amount,
                    'payment_method' => $request->payment_method,
                    'payment_date' => now()->toDateString(),
                    'notes' => 'Pagamento final - quitação completa',
                ]);

                $this->financialService->syncDebtPaymentTransaction($payment);
            }

            $debt->update(['status' => 'paid', 'remaining_amount' => 0]);

            $sale = null;
            if ($debt->isProductDebt() && $request->get('create_sale', true)) {
                $sale = $this->createSaleFromDebt($debt);
            }

            DB::commit();

            $message = $sale ? "Dívida quitada! Venda #{$sale->id} criada." : 'Dívida quitada com sucesso!';

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao marcar como paga: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erro ao quitar dívida'], 500);
        }
    }

    /**
     * Criar venda manual
     */
    public function createManualSale(Debt $debt)
    {
        if (! $debt->isProductDebt() || $debt->status !== 'paid' || $debt->generated_sale_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível criar venda para esta dívida',
            ], 400);
        }

        try {
            DB::beginTransaction();
            $sale = $this->createSaleFromDebt($debt);
            DB::commit();

            if ($sale) {
                return response()->json([
                    'success' => true,
                    'message' => "Venda #{$sale->id} criada!",
                    'sale_id' => $sale->id,
                    'redirect' => route('sales.show', $sale),
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Erro ao criar venda'], 500);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar venda manual: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erro: '.$e->getMessage()], 500);
        }
    }

    /**
     * Dados para edição (AJAX)
     */
    public function editData(Debt $debt)
    {
        try {
            $debt->load(['items.product', 'employee']);

            return response()->json(['success' => true, 'data' => $debt]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao carregar dados'], 500);
        }
    }

    /**
     * Criar dívida a partir de venda
     */
    public function storeFromSale(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'products' => 'required|string',
        ]);

        try {
            $products = json_decode($request->products, true);

            if (empty($products) || ! is_array($products) || count($products) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum produto fornecido',
                ], 400);
            }

            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($products as $p) {
                $totalAmount += $p['quantity'] * $p['unit_price'];
            }

            $debt = Debt::create([
                'debt_type' => 'product',
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'original_amount' => $totalAmount,
                'remaining_amount' => $totalAmount,
                'debt_date' => $request->debt_date,
                'due_date' => $request->due_date ?: now()->addDays(30)->toDateString(),
                'status' => 'active',
                'description' => $request->description,
                'notes' => $request->notes,
            ]);

            foreach ($products as $productData) {
                $product = Product::findOrFail($productData['product_id']);

                if ($product->type === 'product' && $product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Estoque insuficiente para {$product->name}");
                }

                DebtItem::create([
                    'debt_id' => $debt->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total_price' => $productData['quantity'] * $productData['unit_price'],
                ]);

                if ($product->type === 'product') {
                    $product->decrement('stock_quantity', $productData['quantity']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'out',
                        'quantity' => $productData['quantity'],
                        'reason' => "Venda convertida em dívida #$debt->id",
                        'reference_id' => $debt->id,
                        'movement_date' => $request->debt_date,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dívida criada com sucesso!',
                'debt_id' => $debt->id,
                'redirect' => route('debts.show', $debt),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar dívida de venda: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deletar dívida (soft delete ou permanente)
     */
    public function destroy(Debt $debt)
    {
        // Só permite deletar se ainda não tem pagamentos e está cancelada
        if ($debt->payments()->count() > 0) {
            return back()->with('error', 'Não é possível deletar uma dívida com pagamentos registrados');
        }

        if ($debt->status !== 'cancelled') {
            return back()->with('error', 'Apenas dívidas canceladas podem ser deletadas');
        }

        try {
            // Deletar itens relacionados primeiro
            DebtItem::where('debt_id', $debt->id)->delete();

            // Deletar a dívida
            $debt->delete();

            return redirect()->route('debts.index')
                ->with('success', 'Dívida deletada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao deletar dívida: '.$e->getMessage());

            return back()->with('error', 'Erro ao deletar dívida');
        }
    }

    /**
     * Exportar relatório de devedores
     */
    public function exportDebtorsReport(Request $request)
    {
        try {
            $query = Debt::whereNotIn('status', ['paid', 'cancelled']);

            if ($request->filled('debt_type')) {
                $query->where('debt_type', $request->debt_type);
            }

            if ($request->filled('customer')) {
                $search = '%'.$request->customer.'%';
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', $search)
                        ->orWhere('employee_name', 'like', $search);
                });
            }

            $debtors = $query->get()->groupBy(function ($debt) {
                return $debt->isProductDebt() ? $debt->customer_name : $debt->employee_name;
            })->map(function ($debts) {
                $first = $debts->first();

                return [
                    'tipo' => $first->debt_type === 'product' ? 'Produtos' : 'Dinheiro',
                    'devedor' => $first->isProductDebt() ? $first->customer_name : $first->employee_name,
                    'telefone' => $first->isProductDebt() ? $first->customer_phone : $first->employee_phone,
                    'num_dividas' => $debts->count(),
                    'valor_total' => $debts->sum('remaining_amount'),
                    'divida_antiga' => $debts->min('debt_date'),
                ];
            })->values();

            $filename = 'relatorio-devedores-'.date('Y-m-d').'.csv';

            $callback = function () use ($debtors) {
                $file = fopen('php://output', 'w');

                // Cabeçalho
                fputcsv($file, ['Tipo', 'Devedor', 'Telefone', 'Nº Dívidas', 'Valor Total', 'Dívida Mais Antiga']);

                // Dados
                foreach ($debtors as $debtor) {
                    fputcsv($file, [
                        $debtor['tipo'],
                        $debtor['devedor'],
                        $debtor['telefone'] ?: 'Não informado',
                        $debtor['num_dividas'],
                        'MT '.number_format($debtor['valor_total'], 2, ',', '.'),
                        date('d/m/Y', strtotime($debtor['divida_antiga'])),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao exportar relatório: '.$e->getMessage());

            return back()->with('error', 'Erro ao exportar relatório');
        }
    }

    /**
     * Buscar funcionários (AJAX)
     */
    public function searchEmployees(Request $request)
    {
        $search = $request->get('q', '');

        $employees = User::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($employees);
    }

    /**
     * Buscar clientes (AJAX) - baseado em dívidas existentes
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->get('q', '');

        $customers = Debt::where('debt_type', 'product')
            ->where(function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            })
            ->select('customer_name', 'customer_phone', 'customer_document')
            ->groupBy('customer_name', 'customer_phone', 'customer_document')
            ->limit(10)
            ->get()
            ->map(function ($debt) {
                return [
                    'name' => $debt->customer_name,
                    'phone' => $debt->customer_phone,
                    'document' => $debt->customer_document,
                ];
            });

        return response()->json($customers);
    }

    /**
     * Atualizar status de dívidas vencidas (utilitário)
     */
    public function updateOverdueStatus()
    {
        try {
            $updatedCount = Debt::where('status', 'active')
                ->where('due_date', '<', now()->toDateString())
                ->update(['status' => 'overdue']);

            return response()->json([
                'success' => true,
                'message' => "{$updatedCount} dívidas marcadas como vencidas.",
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status',
            ], 500);
        }
    }
}
