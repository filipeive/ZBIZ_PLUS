<?php

namespace App\Http\Controllers;

use App\Models\BranchProductInquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchProductInquiryController extends Controller
{
    /**
     * List inquiries received by the current branch, scoped to tenant.
     */
    public function index(): View
    {
        $tenantId = current_tenant_id();
        $branchId = current_branch_id();

        $inquiries = BranchProductInquiry::query()
            ->forTenant($tenantId)
            ->receivedByBranch($branchId)
            ->with(['senderBranch', 'recipientBranch', 'product', 'user', 'responseBy'])
            ->latest('created_at')
            ->paginate(25);

        $receivedCount = BranchProductInquiry::query()
            ->forTenant($tenantId)
            ->receivedByBranch($branchId)
            ->pending()
            ->count();

        $unreadCount = BranchProductInquiry::query()
            ->forTenant($tenantId)
            ->receivedByBranch($branchId)
            ->whereNull('read_at')
            ->count();

        $branches = current_tenant()?->branches()->where('is_active', true)->orderBy('name')->get();

        return view('branch-product-inquiries.index', compact('inquiries', 'receivedCount', 'unreadCount', 'branches'));
    }

    /**
     * Store a new product inquiry sent from the current branch.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenantId = current_tenant_id();
        $senderBranchId = current_branch_id();

        $validated = $request->validate([
            'recipient_branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'message' => 'required|string|max:2000',
        ]);

        if ($validated['recipient_branch_id'] == $senderBranchId) {
            return back()->with('error', 'Não é possível enviar uma consulta para a mesma filial.')->withInput();
        }

        $recipientBranch = current_tenant()?->branches()->find($validated['recipient_branch_id']);

        BranchProductInquiry::create([
            'tenant_id' => $tenantId,
            'branch_id' => $senderBranchId,
            'sender_branch_id' => $senderBranchId,
            'recipient_branch_id' => $validated['recipient_branch_id'],
            'product_id' => $validated['product_id'],
            'product_name' => $validated['product_name'],
            'user_id' => auth()->id(),
            'quantity' => $validated['quantity'],
            'message' => $validated['message'],
            'status' => BranchProductInquiry::STATUS_PENDING,
        ]);

        return redirect()->route('branch-product-inquiries.index')->with('success', 'Consulta enviada com sucesso para ' . ($recipientBranch?->name ?? 'a filial selecionada') . '.');
    }

    /**
     * Respond to a product inquiry.
     */
    public function respond(Request $request, BranchProductInquiry $inquiry): RedirectResponse
    {
        $this->authorizeInquiry($inquiry);

        if (!$inquiry->isPending()) {
            return back()->with('error', 'Apenas consultas pendentes podem ser respondidas.')->withInput();
        }

        $validated = $request->validate([
            'response' => 'required|string|max:4000',
        ]);

        $inquiry->update([
            'response' => $validated['response'],
            'response_by' => auth()->id(),
            'status' => BranchProductInquiry::STATUS_RESPONDED,
        ]);

        return back()->with('success', 'Resposta enviada com sucesso.');
    }

    /**
     * Mark an inquiry as read.
     */
    public function markAsRead(BranchProductInquiry $inquiry): RedirectResponse
    {
        $this->authorizeInquiry($inquiry);

        if ($inquiry->read_at === null) {
            $inquiry->update([
                'read_at' => now(),
            ]);
        }

        return back()->with('success', 'Consulta marcada como lida.');
    }

    /**
     * Cancel a pending inquiry.
     */
    public function cancel(BranchProductInquiry $inquiry): RedirectResponse
    {
        $this->authorizeInquiry($inquiry, true);

        if (!$inquiry->isPending()) {
            return back()->with('error', 'Apenas consultas pendentes podem ser canceladas.')->withInput();
        }

        $inquiry->update([
            'status' => BranchProductInquiry::STATUS_CANCELLED,
        ]);

        return back()->with('success', 'Consulta cancelada com sucesso.');
    }

    /**
     * Authorize that the inquiry belongs to the current tenant and is either received by or sent from the current branch.
     */
    protected function authorizeInquiry(BranchProductInquiry $inquiry, bool $requireSender = false): void
    {
        $branchId = current_branch_id();

        if ($inquiry->tenant_id !== current_tenant_id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($requireSender) {
            if ($inquiry->sender_branch_id !== $branchId) {
                abort(403, 'Apenas a filial remetente pode cancelar esta consulta.');
            }
        } else {
            if ($inquiry->recipient_branch_id !== $branchId) {
                abort(403, 'Acesso não autorizado a esta consulta.');
            }
        }
    }
}
