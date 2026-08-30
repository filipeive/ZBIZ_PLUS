<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Listar categorias de despesas.
     */
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->paginate(8);

        return view('expenses.categories.index', compact('categories'));
    }

    /**
     * Mostrar formulário de criação (opcional, caso precise).
     */
    public function create()
    {
        return redirect()->route('expense-categories.index');
    }

    /**
     * Salvar nova categoria.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:500',
            'is_operational' => 'nullable|boolean',
            'is_rent' => 'nullable|boolean',
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',
        ]);

        $validated['is_operational'] = $request->boolean('is_operational');
        $validated['is_rent'] = $request->boolean('is_rent');

        if ($validated['is_rent']) {
            $validated['is_operational'] = true;
        }

        $category = ExpenseCategory::create($validated);

        // Toast notification (se disponível)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso!',
                'category' => $category
            ]);
        }

        return redirect()->route('expense-categories.index')
            ->with('success', "Categoria \"{$category->name}\" criada com sucesso!");
    }

    /**
     * Mostrar categoria específica.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load(['expenses' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('expenses.categories.show', compact('expenseCategory'));
    }

    /**
     * Mostrar formulário de edição.
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        // Redirecionar para index, pois agora usamos modal
        return redirect()->route('expense-categories.index');
    }

    /**
     * Atualizar categoria.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string|max:500',
            'is_operational' => 'nullable|boolean',
            'is_rent' => 'nullable|boolean',
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',
        ]);

        $validated['is_operational'] = $request->boolean('is_operational');
        $validated['is_rent'] = $request->boolean('is_rent');

        if ($validated['is_rent']) {
            $validated['is_operational'] = true;
        }

        $expenseCategory->update($validated);

        // Toast notification (se disponível)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso!',
                'category' => $expenseCategory
            ]);
        }

        return redirect()->route('expense-categories.index')
            ->with('success', "Categoria \"{$expenseCategory->name}\" atualizada com sucesso!");
    }

    /**
     * Apagar categoria.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Verificar se há despesas associadas
        $expensesCount = $expenseCategory->expenses()->count();
        
        if ($expensesCount > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', "Esta categoria possui {$expensesCount} despesa(s) associada(s) e não pode ser excluída. Remova ou reatribua as despesas primeiro.");
        }

        $categoryName = $expenseCategory->name;
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', "Categoria \"{$categoryName}\" excluída com sucesso!");
    }

    /**
     * API: Buscar categorias (para autocomplete/select).
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $categories = ExpenseCategory::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'description']);

        return response()->json($categories);
    }

    /**
     * API: Estatísticas das categorias.
     */
    public function stats()
    {
        $stats = [
            'total_categories' => ExpenseCategory::count(),
            'active_categories' => ExpenseCategory::has('expenses')->count(),
            'empty_categories' => ExpenseCategory::doesntHave('expenses')->count(),
            'most_used' => ExpenseCategory::withCount('expenses')
                ->orderBy('expenses_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'expenses_count']),
        ];

        return response()->json($stats);
    }
}
