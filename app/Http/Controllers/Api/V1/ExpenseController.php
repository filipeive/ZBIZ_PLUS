<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Expense::where('tenant_id', $this->tenantId($request))
            ->with(['category', 'user']);

        if ($request->filled('from')) {
            $query->where('expense_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('expense_date', '<=', $request->to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate($request->get('per_page', 20));

        return response()->json($expenses);
    }

    public function show(Request $request, Expense $expense): JsonResponse
    {
        return response()->json(['data' => $expense->load(['category', 'user'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id'    => 'required|exists:expense_categories,id',
            'amount'         => 'required|numeric|min:0.01',
            'description'    => 'required|string|max:255',
            'payment_method' => 'required|string',
            'expense_date'   => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $data['tenant_id'] = $this->tenantId($request);
        $data['branch_id'] = $request->user()?->branch_id;
        $data['user_id']   = $request->user()?->id;

        $expense = Expense::create($data);

        return response()->json(['message' => 'Despesa registada.', 'data' => $expense], 201);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $data = $request->validate([
            'amount'      => 'sometimes|numeric|min:0.01',
            'description' => 'sometimes|string|max:255',
            'notes'       => 'nullable|string',
        ]);
        $expense->update($data);
        return response()->json(['message' => 'Despesa atualizada.', 'data' => $expense]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();
        return response()->json(['message' => 'Despesa eliminada.']);
    }
}
