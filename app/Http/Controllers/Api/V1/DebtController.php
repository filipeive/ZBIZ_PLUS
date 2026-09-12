<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DebtController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $debts = Debt::where('tenant_id', $this->tenantId($request))
            ->with(['customer', 'debtItems', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($debts);
    }

    public function show(Request $request, Debt $debt): JsonResponse
    {
        return response()->json(['data' => $debt->load(['customer', 'debtItems', 'payments'])]);
    }

    public function overdueDebts(Request $request): JsonResponse
    {
        $debts = Debt::where('tenant_id', $this->tenantId($request))
            ->where('status', 'overdue')
            ->orWhere(function ($q) use ($request) {
                $q->where('tenant_id', $this->tenantId($request))
                  ->whereIn('status', ['pending', 'partial'])
                  ->where('due_date', '<', now());
            })
            ->with('customer')
            ->get();

        return response()->json(['data' => $debts, 'total' => $debts->count()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'customer_id'     => 'nullable|exists:customers,id',
            'total_amount'    => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        $data['tenant_id']       = $this->tenantId($request);
        $data['branch_id']       = $request->user()?->branch_id;
        $data['user_id']         = $request->user()?->id;
        $data['remaining_amount']= $data['total_amount'];
        $data['status']          = 'pending';

        $debt = Debt::create($data);

        return response()->json(['message' => 'Dívida registada.', 'data' => $debt], 201);
    }

    public function storeFromSale(Request $request): JsonResponse
    {
        $request->validate(['sale_id' => 'required|exists:sales,id']);
        return response()->json(['message' => 'Dívida criada a partir da venda.'], 201);
    }

    public function update(Request $request, Debt $debt): JsonResponse
    {
        $data = $request->validate([
            'due_date' => 'nullable|date',
            'notes'    => 'nullable|string',
        ]);
        $debt->update($data);
        return response()->json(['message' => 'Dívida atualizada.', 'data' => $debt]);
    }

    public function addPayment(Request $request, Debt $debt): JsonResponse
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        $payment = DebtPayment::create([
            'debt_id'        => $debt->id,
            'tenant_id'      => $debt->tenant_id,
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_date'   => now()->toDateString(),
            'notes'          => $data['notes'] ?? null,
        ]);

        $remaining = max(0, $debt->remaining_amount - $data['amount']);
        $status = $remaining == 0 ? 'paid' : 'partial';
        $debt->update(['remaining_amount' => $remaining, 'status' => $status]);

        return response()->json(['message' => 'Pagamento registado.', 'data' => $payment]);
    }

    public function markAsPaid(Debt $debt): JsonResponse
    {
        $debt->update(['remaining_amount' => 0, 'status' => 'paid']);
        return response()->json(['message' => 'Dívida marcada como paga.']);
    }

    public function cancel(Debt $debt): JsonResponse
    {
        $debt->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Dívida cancelada.']);
    }

    public function destroy(Debt $debt): JsonResponse
    {
        $debt->delete();
        return response()->json(['message' => 'Dívida eliminada.']);
    }

    public function searchCustomers(Request $request, string $term): JsonResponse
    {
        $customers = Customer::where('tenant_id', $this->tenantId($request))
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function searchEmployees(Request $request, string $term): JsonResponse
    {
        $employees = User::where('tenant_id', $this->tenantId($request))
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'role_id', 'job_title']);

        return response()->json(['data' => $employees]);
    }
}
