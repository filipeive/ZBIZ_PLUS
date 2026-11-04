<?php

namespace App\Http\Requests\Pos;

use App\Models\CashShift;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePosSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Resolve o Tenant ID exclusivamente a partir do contexto autenticado (nunca do payload do cliente).
     */
    public function tenantId(): int
    {
        return (int) (current_tenant_id() ?? $this->user()?->tenant_id ?? 0);
    }

    /**
     * Resolve a Filial (Branch) ativa a partir do contexto autenticado.
     */
    public function branchId(): ?int
    {
        $branchId = current_branch_id() ?? $this->user()?->branch_id;

        return $branchId !== null ? (int) $branchId : null;
    }

    public function pricesIncludeTax(): bool
    {
        return filter_var($this->input('prices_include_tax', true), FILTER_VALIDATE_BOOLEAN);
    }

    protected function prepareForValidation(): void
    {
        // Garantir que tenant_id nunca seja injetado pelo cliente
        $this->request->remove('tenant_id');
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'customer_id' => [
                'nullable',
                Rule::exists('customers', 'id')->where('tenant_id', $tenantId),
            ],
            'customer_name'      => ['nullable', 'string', 'max:150'],
            'customer_nuit'      => ['nullable', 'string', 'max:15'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where('tenant_id', $tenantId),
            ],
            'items.*.quantity'   => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount'   => ['nullable', 'numeric', 'min:0'],
            'discount_amount'    => ['nullable', 'numeric', 'min:0'],
            'tax_regime'         => ['nullable', 'string', 'in:normal,exempt'],
            'tax_rate'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'prices_include_tax' => ['nullable', 'boolean'],
            'payment_method'     => ['required', 'string', 'in:cash,mpesa,emola,card,credit,split'],
            'amount_paid'        => ['required', 'numeric', 'min:0'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'offline_id'         => ['nullable', 'string', 'max:120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $tenantId = $this->tenantId();
            if ($tenantId <= 0) {
                $validator->errors()->add('tenant_id', 'Tenant não identificado para esta operação POS.');
                return;
            }

            $user = $this->user();
            if ($user && !$user->isAdmin()) {
                $hasTodayShift = CashShift::where('tenant_id', $tenantId)
                    ->where('branch_id', $this->branchId())
                    ->where('user_id', $user->id)
                    ->where('status', 'open')
                    ->whereDate('opened_at', today())
                    ->exists();

                if (!$hasTodayShift) {
                    $validator->errors()->add('cash_shift', 'Abra o caixa de hoje antes de registar uma venda.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_id.exists'          => 'O cliente seleccionado não pertence a este estabelecimento.',
            'items.required'              => 'Adicione pelo menos um artigo ou serviço ao cesto antes de finalizar a venda.',
            'items.array'                 => 'A lista de artigos da venda é inválida.',
            'items.min'                   => 'O cesto de venda deve conter pelo menos um artigo.',
            'items.*.product_id.required' => 'Um dos artigos no cesto não foi identificado.',
            'items.*.product_id.exists'   => 'O artigo seleccionado não existe ou não pertence a esta empresa.',
            'items.*.quantity.required'   => 'Informe a quantidade para todos os artigos do cesto.',
            'items.*.quantity.numeric'    => 'A quantidade do artigo deve ser um valor numérico válido.',
            'items.*.quantity.min'        => 'A quantidade mínima permitida por artigo é 0,01.',
            'items.*.unit_price.required' => 'O preço unitário do artigo é obrigatório.',
            'items.*.unit_price.min'      => 'O preço unitário do artigo não pode ser negativo.',
            'payment_method.required'     => 'Seleccione o meio de pagamento (Numerário, M-Pesa, e-Mola, POS ou Fiado).',
            'payment_method.in'           => 'O meio de pagamento seleccionado não é suportado.',
            'amount_paid.required'        => 'Informe o valor entregue pelo cliente.',
            'amount_paid.numeric'         => 'O valor entregue deve ser numérico (em Meticais).',
            'amount_paid.min'             => 'O valor entregue não pode ser negativo.',
            'tax_regime.in'               => 'Regime de IVA inválido. Utilize regime normal (16%) ou isento (Art. 9º CIVA).',
        ];
    }
}
