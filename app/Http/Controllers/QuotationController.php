<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $query = Quotation::with(['customer', 'user', 'convertedSale'])
            ->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_nuit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $quotations = $query->latest('date')->paginate(12);

        $counts = [
            'all'       => Quotation::where('tenant_id', $tenantId)->count(),
            'draft'     => Quotation::where('tenant_id', $tenantId)->where('status', 'draft')->count(),
            'sent'      => Quotation::where('tenant_id', $tenantId)->where('status', 'sent')->count(),
            'approved'  => Quotation::where('tenant_id', $tenantId)->where('status', 'approved')->count(),
            'converted' => Quotation::where('tenant_id', $tenantId)->where('status', 'converted')->count(),
        ];

        return view('quotations.index', compact('quotations', 'counts'));
    }

    public function create(): View
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($tenant, 404);

        $tenantId = $tenant->id;
        $docSettings = $tenant->getDocumentSettings();

        $customers = Customer::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $nextNumber = Quotation::generateNextNumber($tenantId);

        return view('quotations.create', compact('tenant', 'docSettings', 'customers', 'products', 'nextNumber'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($tenant, 403);

        $validated = $request->validate([
            'customer_id'          => 'nullable|exists:customers,id',
            'customer_name'        => 'required|string|max:255',
            'customer_nuit'        => 'nullable|string|max:25',
            'customer_email'       => 'nullable|email|max:255',
            'customer_phone'       => 'nullable|string|max:50',
            'customer_address'     => 'nullable|string|max:255',
            'date'                 => 'required|date',
            'valid_until'          => 'nullable|date|after_or_equal:date',
            'tax_regime'           => 'required|in:normal,exempt,simplified',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'tax_exemption_reason' => 'nullable|string|max:255',
            'prices_include_tax'   => 'nullable|boolean',
            'notes'                => 'nullable|string',
            'terms_conditions'     => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'nullable|exists:products,id',
            'items.*.item_name'    => 'required|string|max:255',
            'items.*.description'  => 'nullable|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.discount'     => 'nullable|numeric|min:0',
            'items.*.is_exempt'    => 'nullable|boolean',
        ]);

        try {
            $quotation = DB::transaction(function () use ($validated, $tenant, $request) {
                $quotationNumber = Quotation::generateNextNumber($tenant->id);

                $quotation = Quotation::create([
                    'tenant_id'            => $tenant->id,
                    'branch_id'            => current_branch_id() ?? auth()->user()?->branch_id,
                    'customer_id'          => $validated['customer_id'] ?? null,
                    'user_id'              => auth()->id(),
                    'quotation_number'     => $quotationNumber,
                    'customer_name'        => $validated['customer_name'],
                    'customer_nuit'        => $validated['customer_nuit'] ?? null,
                    'customer_email'       => $validated['customer_email'] ?? null,
                    'customer_phone'       => $validated['customer_phone'] ?? null,
                    'customer_address'     => $validated['customer_address'] ?? null,
                    'date'                 => $validated['date'],
                    'valid_until'          => $validated['valid_until'] ?? null,
                    'tax_regime'           => $validated['tax_regime'],
                    'tax_rate'             => (float)($validated['tax_rate'] ?? 16.0),
                    'tax_exemption_reason' => $validated['tax_exemption_reason'] ?? null,
                    'prices_include_tax'   => $request->boolean('prices_include_tax', true),
                    'status'               => 'draft',
                    'notes'                => $validated['notes'] ?? null,
                    'terms_conditions'     => $validated['terms_conditions'] ?? null,
                ]);

                foreach ($validated['items'] as $itemData) {
                    $quotation->items()->create([
                        'tenant_id'       => $tenant->id,
                        'product_id'      => $itemData['product_id'] ?? null,
                        'item_name'       => $itemData['item_name'],
                        'description'     => $itemData['description'] ?? null,
                        'quantity'        => (float)$itemData['quantity'],
                        'unit_price'      => (float)$itemData['unit_price'],
                        'discount_amount' => (float)($itemData['discount'] ?? 0),
                        'tax_rate'        => (float)($validated['tax_rate'] ?? 16.0),
                        'is_tax_exempt'   => !empty($itemData['is_exempt']),
                    ]);
                }

                $quotation->calculateTotals();

                return $quotation;
            });

            return redirect()->route('quotations.show', $quotation)
                ->with('success', "Cotação {$quotation->quotation_number} criada com sucesso!");
        } catch (\Exception $e) {
            Log::error('Erro ao criar cotação: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao salvar cotação: ' . $e->getMessage());
        }
    }

    public function show(Quotation $quotation): View
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($quotation->tenant_id === $tenant?->id, 403);

        $quotation->load(['items.product', 'customer', 'user', 'convertedSale']);
        $docSettings = $tenant->getDocumentSettings();

        return view('quotations.show', compact('quotation', 'tenant', 'docSettings'));
    }

    public function downloadPdf(Quotation $quotation)
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($quotation->tenant_id === $tenant?->id, 403);

        $quotation->load(['items.product', 'customer', 'user']);

        $pdf = Pdf::loadView('documents.templates.quotation_pdf', compact('quotation', 'tenant'))
            ->setPaper('a4', 'portrait');

        $cleanNumber = str_replace('/', '_', $quotation->quotation_number);
        return $pdf->download("Cotacao_{$cleanNumber}.pdf");
    }

    /**
     * Converter Cotação em Factura / Venda
     */
    public function convertToSale(Request $request, Quotation $quotation): RedirectResponse
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($quotation->tenant_id === $tenant?->id, 403);

        if (!$quotation->canBeConverted()) {
            return back()->with('error', 'Esta cotação já foi convertida ou não se encontra elegível.');
        }

        $validated = $request->validate([
            'invoice_type'   => 'required|in:cash_invoice,invoice,proforma',
            'payment_method' => 'required|in:cash,card,transfer,mpesa,emola,credit',
            'due_date'       => 'nullable|date',
        ]);

        try {
            $sale = DB::transaction(function () use ($quotation, $tenant, $validated) {
                $invoiceType = $validated['invoice_type'];
                $invoiceNumber = Sale::generateNextInvoiceNumber($tenant->id, $invoiceType);

                $sale = Sale::create([
                    'tenant_id'            => $tenant->id,
                    'branch_id'            => $quotation->branch_id ?? current_branch_id() ?? auth()->user()?->branch_id,
                    'user_id'              => auth()->id(),
                    'customer_id'          => $quotation->customer_id,
                    'customer_name'        => $quotation->customer_name,
                    'customer_phone'       => $quotation->customer_phone,
                    'customer_nuit'        => $quotation->customer_nuit,
                    'customer_address'     => $quotation->customer_address,
                    'subtotal'             => $quotation->subtotal,
                    'discount_amount'      => $quotation->discount_amount,
                    'tax_regime'           => $quotation->tax_regime,
                    'tax_rate'             => $quotation->tax_rate,
                    'tax_amount'           => $quotation->tax_amount,
                    'total_amount'         => $quotation->total_amount,
                    'tax_exemption_reason' => $quotation->tax_exemption_reason,
                    'prices_include_tax'   => $quotation->prices_include_tax,
                    'invoice_type'         => $invoiceType,
                    'invoice_number'       => $invoiceNumber,
                    'due_date'             => $validated['due_date'] ?? ($invoiceType === 'invoice' ? now()->addDays(30) : null),
                    'payment_method'       => $validated['payment_method'],
                    'sale_date'            => now(),
                    'quotation_id'         => $quotation->id,
                    'notes'                => "Convertido da Cotação {$quotation->quotation_number}. " . ($quotation->notes ?? ''),
                ]);

                // Criar itens da venda e baixar estoque
                foreach ($quotation->items as $qItem) {
                    $saleItem = SaleItem::create([
                        'tenant_id'           => $tenant->id,
                        'branch_id'           => $sale->branch_id,
                        'sale_id'             => $sale->id,
                        'product_id'          => $qItem->product_id,
                        'quantity'            => $qItem->quantity,
                        'original_unit_price' => $qItem->unit_price,
                        'unit_price'          => $qItem->unit_price,
                        'discount_amount'     => $qItem->discount_amount,
                        'tax_rate'            => $qItem->tax_rate,
                        'tax_amount'          => $qItem->tax_amount,
                        'total_price'         => $qItem->total_price,
                        'is_tax_exempt'       => $qItem->is_tax_exempt,
                    ]);

                    // Baixar stock do produto se for produto físico
                    if ($qItem->product_id) {
                        $product = Product::find($qItem->product_id);
                        if ($product && $product->type === 'product') {
                            $product->updateStock($qItem->quantity, 'out', auth()->id(), "Conversão da Cotação {$quotation->quotation_number}", $sale->id);
                        }
                    }
                }

                // Se for venda a crédito (Factura a Prazo), gerar registo de dívida/conta a receber
                if ($validated['payment_method'] === 'credit' || $invoiceType === 'invoice') {
                    $debt = Debt::create([
                        'tenant_id'         => $tenant->id,
                        'branch_id'         => $sale->branch_id,
                        'customer_id'       => $quotation->customer_id,
                        'sale_id'           => $sale->id,
                        'customer_name'     => $quotation->customer_name,
                        'customer_phone'    => $quotation->customer_phone,
                        'total_amount'      => $sale->total_amount,
                        'paid_amount'       => 0,
                        'remaining_amount'  => $sale->total_amount,
                        'due_date'          => $sale->due_date ?? now()->addDays(30),
                        'status'            => 'active',
                        'notes'             => "Factura a Prazo {$sale->invoice_number} gerada da Cotação {$quotation->quotation_number}",
                    ]);

                    foreach ($sale->items as $sItem) {
                        DebtItem::create([
                            'tenant_id'   => $tenant->id,
                            'branch_id'   => $sale->branch_id,
                            'debt_id'     => $debt->id,
                            'product_id'  => $sItem->product_id,
                            'quantity'    => $sItem->quantity,
                            'unit_price'  => $sItem->unit_price,
                            'total_price' => $sItem->total_price,
                        ]);
                    }

                    if ($quotation->customer) {
                        $quotation->customer->recalculateDebt();
                    }
                }

                // Atualizar cotação como convertida
                $quotation->update([
                    'status'            => 'converted',
                    'converted_sale_id' => $sale->id,
                    'converted_at'      => now(),
                ]);

                return $sale;
            });

            return redirect()->route('sales.show', $sale)
                ->with('success', "Cotação convertida com sucesso em {$sale->official_invoice_title} ({$sale->invoice_number})!");
        } catch (\Exception $e) {
            Log::error('Erro ao converter cotação em venda: ' . $e->getMessage());
            return back()->with('error', 'Falha ao converter cotação: ' . $e->getMessage());
        }
    }
}

