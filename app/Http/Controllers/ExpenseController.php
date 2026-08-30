<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function __construct(private FinancialService $financialService) {}

    /**
     * Exibir lista de despesas com filtros
     */
    public function index(Request $request)
    {
        return $this->renderExpenseIndex($request, false);
    }

    public function operational(Request $request)
    {
        return $this->renderExpenseIndex($request, true);
    }

    private function renderExpenseIndex(Request $request, bool $operationalOnly)
    {
        // Agora buscamos todas as saídas financeiras confirmadas, unificando despesas e pagamentos de salários
        $query = \App\Models\FinancialTransaction::with(['user', 'account', 'reference'])
            ->outflows()
            ->confirmed();

        // Excluir ajustes técnicos da lista de despesas (ajustes não são gastos reais)
        $query->whereNotIn('type', ['cash_adjustment_out']);

        // Somente Admin e Super Admin veem todas as transações. 
        // Gerentes e outros usuários veem apenas o que registraram.
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($operationalOnly) {
            // No FinancialTransaction, despesas operacionais são identificadas pelo tipo ou pela referência
            $query->where(function ($subQuery) {
                $subQuery->whereIn('type', ['expense', 'expense_payment'])
                    ->whereHasMorph('reference', [\App\Models\Expense::class], function ($q) {
                        $q->whereNotNull('product_id')
                            ->orWhereHas('category', fn ($cat) => $cat->where('is_operational', true));
                    })
                    ->orWhereIn('type', ['payroll', 'salary_payment']); // Salários são sempre operacionais
            });
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(10);

        // Totais devem respeitar a flag de inclusão nas métricas
        $metricsQuery = (clone $query)->inMetrics();
        $totalExpenses = $metricsQuery->sum('amount');
        $averageExpense = $metricsQuery->avg('amount') ?: 0;
        $highestExpense = $metricsQuery->max('amount') ?: 0;
        $lowestExpense = $metricsQuery->min('amount') ?: 0;

        // Carregar categorias para o offcanvas
        $categories = $operationalOnly
            ? ExpenseCategory::operational()->orderBy('name')->get()
            : ExpenseCategory::orderBy('name')->get();
        $financialAccounts = FinancialAccount::operational()->orderBy('sort_order')->get();
        $products = Product::where('type', 'product')->where('is_active', true)->orderBy('name')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'total' => $transactions->total(),
                    'per_page' => $transactions->perPage(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
                ],
            ]);
        }

        $pageMode = $operationalOnly ? 'operational' : 'all';
        $pageTitle = $operationalOnly ? 'Despesas Operacionais' : 'Despesas';
        $pageSubtitle = $operationalOnly
            ? 'Controle de renda, compras operacionais e custos ligados ao funcionamento do negócio'
            : 'Registre e acompanhe todas as despesas da reprografia';

        return view('expenses.index', [
            'expenses' => $transactions, // Passamos como 'expenses' para manter compatibilidade com a variável no Blade
            'totalExpenses' => $totalExpenses,
            'averageExpense' => $averageExpense,
            'highestExpense' => $highestExpense,
            'lowestExpense' => $lowestExpense,
            'categories' => $categories,
            'financialAccounts' => $financialAccounts,
            'products' => $products,
            'pageMode' => $pageMode,
            'pageTitle' => $pageTitle,
            'pageSubtitle' => $pageSubtitle,
        ]);
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        // Redirecionar para o index que agora usa modais
        return redirect()->route('expenses.index');
    }

    /**
     * Armazenar nova despesa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'receipt_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $productId = $request->filled('product_id') ? $request->product_id : null;
        $quantity = $request->filled('quantity') ? $request->quantity : 1;

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('expense-receipts', 'public');
        }

        try {
            $expense = DB::transaction(function () use ($validated, $productId, $quantity, $receiptPath) {
                $expense = Expense::create([
                    'user_id' => auth()->id(),
                    'expense_category_id' => $validated['expense_category_id'],
                    'financial_account_id' => $validated['financial_account_id'],
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'expense_date' => $validated['expense_date'],
                    'receipt_number' => $validated['receipt_number'],
                    'notes' => $validated['notes'],
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'receipt_file_path' => $receiptPath,
                ]);

                if ($productId) {
                    $product = Product::findOrFail($productId);
                    $product->increment('stock_quantity', $quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'movement_type' => 'in',
                        'quantity' => $quantity,
                        'reason' => "Compra - Despesa #$expense->id",
                        'reference_id' => $expense->id,
                        'movement_date' => $validated['expense_date'],
                    ]);
                }

                // This will throw an Exception if balance is insufficient
                $this->financialService->syncExpenseTransaction($expense, true);

                return $expense;
            });
        } catch (\Exception $e) {
            // Clean up uploaded file if transaction failed
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }

            $errorMessage = str_contains($e->getMessage(), 'Saldo insuficiente')
                ? $e->getMessage()
                : 'Erro ao registrar despesa: ' . $e->getMessage();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 422);
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa registrada com sucesso!',
                'data' => $expense->load(['user', 'category', 'financialAccount']),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa registrada com sucesso!');
    }

    /**
     * Exibir despesa específica
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'financialAccount']);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Exibir dados detalhados da despesa (API/Modal)
     */
    public function showData(Expense $expense)
    {
        $expense->load(['category', 'user', 'financialAccount']);

        return response()->json([
            'success' => true,
            'data' => $expense
        ]);
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(Expense $expense)
    {
        // $this->authorize('update-expense', $expense);

        $categories = ExpenseCategory::all();
        $financialAccounts = FinancialAccount::operational()->orderBy('sort_order')->get();
        $products = Product::where('type', 'product')->where('is_active', true)->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'financialAccounts', 'products'));
    }

    /**
     * Atualizar despesa
     */
    public function update(Request $request, Expense $expense)
    {
        // Verificar permissão
        // $this->authorize('update-expense', $expense);

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $oldProductId = $expense->product_id;
        $oldQuantity = $expense->quantity ?? 1;
        $newProductId = $request->filled('product_id') ? $request->product_id : null;
        $newQuantity = $request->filled('quantity') ? $request->quantity : 1;

        if ($oldProductId) {
            $oldProduct = Product::find($oldProductId);
            if ($oldProduct) {
                $oldProduct->decrement('stock_quantity', $oldQuantity);
                StockMovement::where('reference_id', $expense->id)
                    ->where('movement_type', 'in')
                    ->delete();
            }
        }

        $expense->update([
            'expense_category_id' => $validated['expense_category_id'],
            'financial_account_id' => $validated['financial_account_id'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_number' => $validated['receipt_number'],
            'notes' => $validated['notes'],
            'product_id' => $newProductId,
            'quantity' => $newQuantity,
        ]);

        if ($newProductId) {
            $newProduct = Product::findOrFail($newProductId);
            $newProduct->increment('stock_quantity', $newQuantity);

            StockMovement::create([
                'product_id' => $newProduct->id,
                'user_id' => auth()->id(),
                'movement_type' => 'in',
                'quantity' => $newQuantity,
                'reason' => "Compra - Despesa #$expense->id",
                'reference_id' => $expense->id,
                'movement_date' => $validated['expense_date'],
            ]);
        }

        $this->financialService->syncExpenseTransaction($expense->fresh());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa atualizada com sucesso!',
                'data' => $expense->fresh()->load(['user', 'category', 'financialAccount']),
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Remover despesa
     */
    public function destroy(Expense $expense, Request $request)
    {
        // $this->authorize('delete', $expense);

        if ($expense->product_id) {
            $product = Product::find($expense->product_id);
            $quantity = $expense->quantity ?? 1;
            if ($product) {
                $product->decrement('stock_quantity', $quantity);
                StockMovement::where('reference_id', $expense->id)
                    ->where('movement_type', 'in')
                    ->delete();
            }
        }

        $this->financialService->removeTransactionsForReference(Expense::class, $expense->id);
        $expense->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Despesa excluída com sucesso!',
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Despesa excluída com sucesso!');
    }

    /**
     * Generate printable rent receipt.
     */
    public function rentReceipt(Expense $expense)
    {
        $expense->loadMissing(['category', 'financialAccount']);

        if (!$expense->isRentExpense()) {
            return redirect()->back()->with('error', 'Esta despesa não é uma categoria de renda.');
        }

        $contract = [
            'landlord_name' => Setting::get('rent_contract_landlord_name', 'Filipe Domingos dos Santos'),
            'tenant_name' => Setting::get('rent_contract_tenant_name', 'Minora Nhatambo Paquete'),
            'property_location' => Setting::get('rent_contract_property_location', 'Avenida Eduardo Mondlane'),
            'business_activity' => Setting::get('rent_contract_business_activity', 'Reprografia, Serigrafia & Escritorio'),
        ];

        $pdf = Pdf::loadView('expenses.rent-receipt', compact('expense', 'contract'));
        
        $filename = 'recibo-renda-' . $expense->expense_date->format('m-Y') . '.pdf';
        
        return $pdf->stream($filename);
    }

    /**
     * Upload signed receipt for an expense.
     */
    public function uploadReceipt(Request $request, Expense $expense)
    {
        $request->validate([
            'receipt_file' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        // Remover arquivo anterior se existir
        if ($expense->receipt_file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->receipt_file_path);
        }

        $path = $request->file('receipt_file')->store('expense-receipts', 'public');
        $expense->update(['receipt_file_path' => $path]);

        return redirect()->back()->with('success', 'Comprovante/Recibo carregado com sucesso.');
    }
}
