<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDebtRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'original_amount' => 'required|numeric|min:0.01|max:999999.99',
            'paid_amount' => 'nullable|numeric|min:0|max:999999.99|lte:original_amount',
            'debt_date' => 'required|date|before_or_equal:today',
            'due_date' => 'nullable|date|after:debt_date',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'sale_id' => 'nullable|exists:sales,id',
            'order_id' => 'nullable|exists:orders,id',
        ];
    }

    public function messages()
    {
        return [
            'customer_name.required' => 'O nome do cliente é obrigatório.',
            'original_amount.required' => 'O valor da dívida é obrigatório.',
            'original_amount.min' => 'O valor da dívida deve ser maior que zero.',
            'paid_amount.lte' => 'O valor pago não pode ser maior que o valor da dívida.',
            'debt_date.required' => 'A data da dívida é obrigatória.',
            'debt_date.before_or_equal' => 'A data da dívida não pode ser futura.',
            'due_date.after' => 'A data de vencimento deve ser posterior à data da dívida.',
        ];
    }
}
