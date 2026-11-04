<?php

namespace App\Http\Requests\CashShifts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpenCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function tenantId(): int
    {
        return (int) (current_tenant_id() ?? $this->user()?->tenant_id ?? 0);
    }

    public function branchId(): ?int
    {
        $branchId = current_branch_id() ?? $this->user()?->branch_id;

        return $branchId !== null ? (int) $branchId : null;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('tenant_id');
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'opening_balance'      => ['required', 'numeric', 'min:0'],
            'financial_account_id' => [
                'nullable',
                Rule::exists('financial_accounts', 'id')->where('tenant_id', $tenantId),
            ],
            'notes'                => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'opening_balance.required'    => 'Informe o fundo de maneio inicial para abertura do caixa.',
            'opening_balance.numeric'     => 'O fundo de maneio deve ser um valor numérico em Meticais.',
            'opening_balance.min'         => 'O fundo de maneio não pode ser negativo.',
            'financial_account_id.exists' => 'A conta de caixa seleccionada não pertence a esta empresa.',
        ];
    }
}
