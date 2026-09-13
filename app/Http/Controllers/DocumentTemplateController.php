<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if (!$tenant) {
            abort(404, 'Empresa não encontrada.');
        }

        $docSettings = $tenant->getDocumentSettings();
        $contract = $this->getRentContractSettings($tenant);

        return view('documents.templates.index', compact('tenant', 'docSettings', 'contract'));
    }

    public function updateSettings(Request $request)
    {
        $tenant = auth()->user()?->tenant;
        if (!$tenant) {
            abort(403, 'Acesso não autorizado.');
        }

        $validated = $request->validate([
            'company_name'           => 'required|string|max:255',
            'legal_name'             => 'nullable|string|max:255',
            'nuit'                   => 'nullable|string|max:25',
            'email'                  => 'nullable|email|max:255',
            'phone'                  => 'nullable|string|max:50',
            'address'                => 'nullable|string|max:255',
            'city'                   => 'nullable|string|max:100',
            'province'               => 'nullable|string|max:100',
            'tax_regime'             => 'required|in:normal,exempt,simplified',
            'tax_rate'               => 'nullable|numeric|min:0|max:100',
            'prices_include_tax'     => 'nullable|boolean',
            'tax_exemption_reason'   => 'nullable|string|max:255',
            'quotation_validity_days'=> 'required|integer|min:1|max:365',
            'invoice_due_days'       => 'required|integer|min:0|max:365',
            'quotation_terms'        => 'nullable|string|max:1000',
            'invoice_terms'          => 'nullable|string|max:1000',
            'footer_notes'           => 'nullable|string|max:500',
            'document_color'         => 'nullable|string|max:20',
            'document_logo'          => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'banks'                  => 'nullable|array',
            'banks.*.bank_name'      => 'nullable|string|max:100',
            'banks.*.account_number' => 'nullable|string|max:100',
            'banks.*.nib'            => 'nullable|string|max:100',
            'banks.*.iban'           => 'nullable|string|max:100',
            'wallets'                => 'nullable|array',
            'wallets.*.wallet_name'  => 'nullable|string|max:100',
            'wallets.*.phone_number' => 'nullable|string|max:50',
            'wallets.*.holder_name'  => 'nullable|string|max:100',
        ]);

        $settings = is_array($tenant->settings) ? $tenant->settings : [];

        // Upload de logótipo se fornecido
        if ($request->hasFile('document_logo')) {
            $path = $request->file('document_logo')->store('tenant_logos', 'public');
            $settings['document_logo'] = 'storage/' . $path;
        }

        // Atualizar campos escalares
        $fieldsToUpdate = [
            'company_name', 'legal_name', 'nuit', 'email', 'phone', 'address',
            'city', 'province', 'tax_regime', 'tax_rate', 'tax_exemption_reason',
            'quotation_validity_days', 'invoice_due_days', 'quotation_terms',
            'invoice_terms', 'footer_notes', 'document_color'
        ];

        foreach ($fieldsToUpdate as $field) {
            if (isset($validated[$field])) {
                $settings[$field] = is_string($validated[$field]) ? trim($validated[$field]) : $validated[$field];
            }
        }

        $settings['prices_include_tax'] = $request->boolean('prices_include_tax', true);

        // Processar contas bancárias preenchidas
        if (!empty($validated['banks'])) {
            $settings['bank_accounts'] = collect($validated['banks'])
                ->filter(fn($b) => !empty($b['bank_name']) && !empty($b['account_number']))
                ->values()
                ->all();
        }

        // Processar carteiras móveis preenchidas
        if (!empty($validated['wallets'])) {
            $settings['mobile_wallets'] = collect($validated['wallets'])
                ->filter(fn($w) => !empty($w['wallet_name']) && !empty($w['phone_number']))
                ->values()
                ->all();
        }

        $tenant->update([
            'name'     => $validated['company_name'],
            'nuit'     => $validated['nuit'] ?? $tenant->nuit,
            'email'    => $validated['email'] ?? $tenant->email,
            'phone'    => $validated['phone'] ?? $tenant->phone,
            'address'  => $validated['address'] ?? $tenant->address,
            'settings' => $settings,
        ]);

        return redirect()->route('document-templates.index')->with('success', 'Configurações de modelos e identidade de documentos salvas com sucesso!');
    }

    public function previewInvoice(string $type = 'invoice')
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        abort_unless($tenant, 404);

        $invoiceType = in_array($type, ['cash_invoice', 'invoice', 'proforma']) ? $type : 'invoice';
        $invoiceNumber = Sale::generateNextInvoiceNumber($tenant->id, $invoiceType);

        // Criar modelo em memória para pré-visualização fiel
        $sale = new Sale([
            'tenant_id'             => $tenant->id,
            'customer_name'         => 'Empresa Modelo & Clientes Lda',
            'customer_phone'        => '+258 84 123 4567',
            'customer_nuit'         => '400123987',
            'customer_address'      => 'Avenida 25 de Setembro, Nº 1420, Maputo',
            'subtotal'              => 12500.00,
            'discount_amount'       => 500.00,
            'total_amount'          => 12000.00,
            'tax_regime'            => $tenant->getDocumentSettings()['tax_regime'] ?? 'normal',
            'tax_rate'              => (float)($tenant->getDocumentSettings()['tax_rate'] ?? 16.0),
            'tax_amount'            => 1655.17,
            'invoice_type'          => $invoiceType,
            'invoice_number'        => $invoiceNumber,
            'due_date'              => now()->addDays(15),
            'payment_method'        => $invoiceType === 'cash_invoice' ? 'M-Pesa / Dinheiro' : 'Transferência Bancária',
            'sale_date'             => now(),
        ]);

        $sale->setRelation('items', collect([
            new SaleItem([
                'product_name'        => 'Computador Portátil Core i5 16GB SSD 512GB',
                'quantity'            => 1,
                'original_unit_price' => 10000.00,
                'unit_price'          => 9500.00,
                'discount_amount'     => 500.00,
                'tax_rate'            => 16.00,
                'tax_amount'          => 1310.34,
                'total_price'         => 9500.00,
                'is_tax_exempt'       => false,
            ]),
            new SaleItem([
                'product_name'        => 'Rato Óptico Sem Fios & Teclado USB',
                'quantity'            => 2,
                'original_unit_price' => 1250.00,
                'unit_price'          => 1250.00,
                'discount_amount'     => 0.00,
                'tax_rate'            => 16.00,
                'tax_amount'          => 344.83,
                'total_price'         => 2500.00,
                'is_tax_exempt'       => false,
            ])
        ]));

        $pdf = Pdf::loadView('documents.templates.invoice_pdf', compact('tenant', 'sale'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Modelo_{$invoiceType}_{$tenant->slug}.pdf");
    }

    public function previewQuotation()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        abort_unless($tenant, 404);

        $quotationNumber = Quotation::generateNextNumber($tenant->id);

        $quotation = new Quotation([
            'tenant_id'             => $tenant->id,
            'quotation_number'      => $quotationNumber,
            'customer_name'         => 'Sociedade Comercial Moçambicana, S.A.',
            'customer_nuit'         => '400555666',
            'customer_email'        => 'compras@sociedade-mocambique.co.mz',
            'customer_phone'        => '+258 86 987 6543',
            'customer_address'      => 'Rua do Comércio, Edifício Sol, Beira',
            'date'                  => now(),
            'valid_until'           => now()->addDays(15),
            'subtotal'              => 45000.00,
            'discount_amount'       => 2000.00,
            'tax_rate'              => 16.00,
            'tax_amount'            => 5931.03,
            'total_amount'          => 43000.00,
            'tax_regime'            => $tenant->getDocumentSettings()['tax_regime'] ?? 'normal',
            'status'                => 'draft',
        ]);

        $quotation->setRelation('items', collect([
            new QuotationItem([
                'item_name'       => 'Fornecimento e Configuração de Equipamentos de Escritório',
                'description'     => 'Instalação de rede local, impressora térmica e posto POS',
                'quantity'        => 1,
                'unit_price'      => 35000.00,
                'discount_amount' => 2000.00,
                'tax_rate'        => 16.00,
                'tax_amount'      => 4551.72,
                'total_price'     => 33000.00,
                'is_tax_exempt'   => false,
            ]),
            new QuotationItem([
                'item_name'       => 'Formação de Operadores e Suporte Técnico 3 Meses',
                'description'     => 'Capacitação prática da equipa de vendas',
                'quantity'        => 1,
                'unit_price'      => 10000.00,
                'discount_amount' => 0.00,
                'tax_rate'        => 16.00,
                'tax_amount'      => 1379.31,
                'total_price'     => 10000.00,
                'is_tax_exempt'   => false,
            ])
        ]));

        $pdf = Pdf::loadView('documents.templates.quotation_pdf', compact('tenant', 'quotation'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("Modelo_Cotacao_{$tenant->slug}.pdf");
    }

    public function updateRentContract(Request $request)
    {
        $tenant = auth()->user()?->tenant;
        abort_unless($tenant, 403);

        $validated = $request->validate([
            'landlord_name'           => 'required|string|max:255',
            'landlord_marital_status' => 'nullable|string|max:100',
            'landlord_document'       => 'nullable|string|max:255',
            'landlord_address'        => 'nullable|string|max:255',
            'tenant_name'             => 'required|string|max:255',
            'tenant_marital_status'   => 'nullable|string|max:100',
            'tenant_document'         => 'nullable|string|max:255',
            'tenant_address'          => 'nullable|string|max:255',
            'property_location'       => 'required|string|max:255',
            'business_activity'       => 'required|string|max:255',
            'contract_start_date'     => 'required|date',
            'contract_term'           => 'nullable|string|max:255',
            'monthly_rent'            => 'required|numeric|min:0',
            'payment_day'             => 'required|integer|min:1|max:31',
            'payment_methods'         => 'nullable|string|max:255',
            'rent_paid_amount'        => 'required|numeric|min:0',
            'rehab_total_investment'  => 'required|numeric|min:0',
            'rehab_monthly_deduction' => 'required|numeric|min:0',
            'rehab_estimated_months'  => 'required|integer|min:0',
            'prior_notice_days'       => 'required|integer|min:0',
            'issue_location'          => 'nullable|string|max:255',
            'rehab_items_text'        => 'nullable|string',
            'special_clauses'         => 'nullable|string',
        ]);

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $settings['rent_contract'] = $validated;
        $tenant->update(['settings' => $settings]);

        return redirect()->route('document-templates.index')->with('success', 'Contrato de arrendamento atualizado com sucesso para esta empresa.');
    }

    public function printRentContract()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        abort_unless($tenant, 404);

        $contract = $this->getRentContractSettings($tenant);

        $pdf = Pdf::loadView('documents.templates.rent-contract-pdf', compact('contract', 'tenant'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("contrato-arrendamento-{$tenant->slug}.pdf");
    }

    public function printPhysicalReceiptBook()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        abort_unless($tenant, 404);

        $contract = $this->getRentContractSettings($tenant);

        $pdf = Pdf::loadView('documents.templates.physical-receipt-book', compact('contract', 'tenant'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("livro-recibos-{$tenant->slug}.pdf");
    }

    private function getRentContractSettings(Tenant $tenant): array
    {
        $tenantSettings = is_array($tenant->settings) ? $tenant->settings : [];
        $saved = $tenantSettings['rent_contract'] ?? [];

        $defaults = [
            'landlord_name'           => 'Proprietário do Imóvel',
            'landlord_marital_status' => 'Casado',
            'landlord_document'       => 'BI / NUIT: 100000000',
            'landlord_address'        => 'Cidade de ' . ($tenantSettings['city'] ?? 'Quelimane'),
            'tenant_name'             => $tenant->name,
            'tenant_marital_status'   => 'Sociedade por Quotas',
            'tenant_document'         => 'NUIT: ' . ($tenant->nuit ?? '400000000'),
            'tenant_address'          => $tenant->address ?? ('Cidade de ' . ($tenantSettings['city'] ?? 'Quelimane')),
            'property_location'       => $tenant->address ?? 'Avenida Principal',
            'business_activity'       => $tenant->business_type_label,
            'contract_start_date'     => date('Y-01-01'),
            'contract_term'           => '1 Ano Renovável',
            'monthly_rent'            => '15000',
            'payment_day'             => '30',
            'payment_methods'         => 'transferência bancária, depósito ou M-Pesa',
            'rent_paid_amount'        => '15000',
            'rehab_total_investment'  => '0',
            'rehab_monthly_deduction' => '0',
            'rehab_estimated_months'  => '0',
            'prior_notice_days'       => '30',
            'issue_location'          => $tenantSettings['city'] ?? 'Quelimane',
            'rehab_items_text'        => "Pintura e acabamentos|0.00\nAdaptação elétrica|0.00",
            'special_clauses'         => 'Qualquer litígio emergente do presente contrato será resolvido preferencialmente de forma amigável entre as partes. Na impossibilidade de acordo, as partes recorrerão aos meios judiciais competentes.',
        ];

        $contract = array_merge($defaults, $saved);

        $contract['rehab_items'] = collect(preg_split("/\\r\\n|\\n|\\r/", (string) ($contract['rehab_items_text'] ?? '')))
            ->filter()
            ->map(function (string $line) {
                [$label, $amount] = array_pad(explode('|', $line, 2), 2, '0');
                return [
                    'label' => trim($label),
                    'amount' => (float) trim($amount),
                ];
            })
            ->values()
            ->all();

        return $contract;
    }
}
