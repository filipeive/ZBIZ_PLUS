<?php

namespace App\Http\Requests\CashShifts;

use App\Models\CashShift;
use Illuminate\Foundation\Http\FormRequest;

class CloseCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        /** @var CashShift|null $shift */
        $shift = $this->route('shift');
        if (!$shift instanceof CashShift) {
            return false;
        }

        $tenantId = (int) (current_tenant_id() ?? $user->tenant_id ?? 0);
        if ((int) $shift->tenant_id !== $tenantId) {
            return false;
        }

        return $user->isAdmin() || (int) $shift->user_id === (int) $user->id;
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('tenant_id');
    }

    public function rules(): array
    {
        return [
            'closing_balance_actual' => ['required', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'closing_balance_actual.required' => 'Declare o valor físico contado em caixa (Fecho Cego) em Meticais.',
            'closing_balance_actual.numeric'  => 'O valor contado em caixa deve ser numérico.',
            'closing_balance_actual.min'      => 'O valor contado em caixa não pode ser negativo.',
        ];
    }
}
