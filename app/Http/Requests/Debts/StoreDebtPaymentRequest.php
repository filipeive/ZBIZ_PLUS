<?php

namespace App\Http\Requests\Debts;

use App\Models\Debt;
use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $debt = $this->route('debt');
        if ($debt instanceof Debt) {
            $tenantId = current_tenant_id() ?? $this->user()?->tenant_id;
            return (int) $debt->tenant_id === (int) $tenantId;
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('tenant_id');
    }

    public function rules(): array
    {
        /** @var Debt|null $debt */
        $debt = $this->route('debt');
        $maxAmount = $debt instanceof Debt ? (float) $debt->remaining_amount : 999999999.99;

        return [
            'amount'         => ['required', 'numeric', 'min:0.01', 'max:' . $maxAmount],
            'payment_method' => ['required', 'string', 'in:cash,card,transfer,mpesa,emola'],
            'payment_date'   => ['required', 'date', 'before_or_equal:today'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'create_sale'    => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'               => 'Informe o valor da amortização em Meticais.',
            'amount.numeric'                => 'O valor da amortização deve ser numérico.',
            'amount.min'                    => 'O valor da amortização deve ser superior a zero.',
            'amount.max'                    => 'O valor da amortização não pode exceder o saldo remanescente da dívida.',
            'payment_method.required'       => 'Seleccione o meio de pagamento (Numerário, M-Pesa, e-Mola, POS ou Transferência).',
            'payment_method.in'             => 'Meio de pagamento inválido. Utilize Numerário, M-Pesa, e-Mola, Cartão/POS ou Transferência.',
            'payment_date.required'         => 'Informe a data do recebimento.',
            'payment_date.date'             => 'A data do recebimento é inválida.',
            'payment_date.before_or_equal'  => 'A data do recebimento não pode ser futura.',
        ];
    }
}
