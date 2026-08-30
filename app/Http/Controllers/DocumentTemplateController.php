<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $contract = $this->getRentContractSettings();

        return view('documents.templates.index', compact('contract'));
    }

    public function updateRentContract(Request $request)
    {
        $validated = $request->validate([
            'landlord_name' => 'required|string|max:255',
            'landlord_marital_status' => 'nullable|string|max:100',
            'landlord_document' => 'nullable|string|max:255',
            'landlord_address' => 'nullable|string|max:255',
            'tenant_name' => 'required|string|max:255',
            'tenant_marital_status' => 'nullable|string|max:100',
            'tenant_document' => 'nullable|string|max:255',
            'tenant_address' => 'nullable|string|max:255',
            'property_location' => 'required|string|max:255',
            'business_activity' => 'required|string|max:255',
            'contract_start_date' => 'required|date',
            'contract_term' => 'nullable|string|max:255',
            'monthly_rent' => 'required|numeric|min:0',
            'payment_day' => 'required|integer|min:1|max:31',
            'payment_methods' => 'nullable|string|max:255',
            'rent_paid_amount' => 'required|numeric|min:0',
            'rehab_total_investment' => 'required|numeric|min:0',
            'rehab_monthly_deduction' => 'required|numeric|min:0',
            'rehab_estimated_months' => 'required|integer|min:0',
            'prior_notice_days' => 'required|integer|min:0',
            'issue_location' => 'nullable|string|max:255',
            'rehab_items_text' => 'nullable|string',
            'special_clauses' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set("rent_contract_{$key}", is_string($value) ? trim($value) : (string) $value, 'documents');
        }

        return redirect()->route('documents.templates.index')->with('success', 'Template do contrato de renda atualizado com sucesso.');
    }

    public function printRentContract()
    {
        $contract = $this->getRentContractSettings();

        $pdf = Pdf::loadView('documents.templates.rent-contract-pdf', compact('contract'));

        return $pdf->stream('contrato-arrendamento-comercial.pdf');
    }

    /**
     * Generate physical receipt book template PDF
     */
    public function printPhysicalReceiptBook()
    {
        $contract = $this->getRentContractSettings();

        $pdf = Pdf::loadView('documents.templates.physical-receipt-book', compact('contract'));

        return $pdf->stream('livro-recibos-template.pdf');
    }

    private function getRentContractSettings(): array
    {
        $defaults = [
            'landlord_name' => 'Filipe Domingos dos Santos',
            'landlord_marital_status' => 'Casado',
            'landlord_document' => 'BI: 0401010262302B',
            'landlord_address' => '1 de Maio B, Cidade de Quelimane',
            'tenant_name' => 'Minora Nhatambo Paquete',
            'tenant_marital_status' => 'Solteira',
            'tenant_document' => 'BI: 041308868254B',
            'tenant_address' => '17 de Setembro - Cidade de Quelimane',
            'property_location' => 'Avenida Eduardo Mondlane',
            'business_activity' => 'Reprografia, Serigrafia & Escritorio',
            'contract_start_date' => '2026-04-01',
            'contract_term' => 'Indeterminado',
            'monthly_rent' => '5000',
            'payment_day' => '31',
            'payment_methods' => 'transferência bancária, depósito ou dinheiro',
            'rent_paid_amount' => '2500',
            'rehab_total_investment' => '21270',
            'rehab_monthly_deduction' => '2500',
            'rehab_estimated_months' => '11',
            'prior_notice_days' => '15',
            'issue_location' => 'Quelimane',
            'rehab_items_text' => implode("\n", [
                'Pedraria (materiais e mão de obra)|9640.00',
                'Carpintaria (materiais e mão de obra)|6240.00',
                'Serralharia (materiais e mão de obra)|2300.00',
                'Electricidade (materiais e mão de obra)|590.00',
                'Nova Ligação Eletrica|1500.00',
                'Pintura|1000.00',
            ]),
            'special_clauses' => 'Qualquer litígio emergente do presente contrato será resolvido preferencialmente de forma amigável entre as partes. Na impossibilidade de acordo, as partes recorrerão aos meios judiciais competentes.',
        ];

        $contract = [];

        foreach ($defaults as $key => $defaultValue) {
            $contract[$key] = Setting::get("rent_contract_{$key}", $defaultValue);
        }

        $contract['rehab_items'] = collect(preg_split("/\\r\\n|\\n|\\r/", (string) $contract['rehab_items_text']))
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
