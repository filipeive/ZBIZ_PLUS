<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $debt = $this->route('debt');
        
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . ($debt ? $debt->remaining_amount : 999999.99),
            ],
            'payment_method' => 'required|in:cash,card,transfer,pix',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'O valor do pagamento é obrigatório.',
            'amount.min' => 'O valor do pagamento deve ser maior que zero.',
            'amount.max' => 'O valor do pagamento não pode ser maior que o valor restante da dívida.',
            'payment_method.required' => 'O método de pagamento é obrigatório.',
            'payment_date.required' => 'A data do pagamento é obrigatória.',
            'payment_date.before_or_equal' => 'A data do pagamento não pode ser futura.',
        ];
    }
}