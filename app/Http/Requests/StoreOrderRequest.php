<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreOrderRequest extends FormRequest
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
            'customer_email' => 'nullable|email|max:100',
            'description' => 'required|string|min:10',
            'estimated_amount' => 'nullable|numeric|min:0|max:999999.99',
            'advance_payment' => 'nullable|numeric|min:0|max:999999.99|lte:estimated_amount',
            'delivery_date' => 'nullable|date|after:today',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity' => 'required|integer|min:1|max:99999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999.99',
            'items.*.product_id' => 'nullable|exists:products,id',
        ];
    }

    public function messages()
    {
        return [
            'customer_name.required' => 'O nome do cliente é obrigatório.',
            'description.required' => 'A descrição do pedido é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 10 caracteres.',
            'advance_payment.lte' => 'O valor da entrada não pode ser maior que o valor estimado.',
            'delivery_date.after' => 'A data de entrega deve ser futura.',
            'items.required' => 'É necessário adicionar pelo menos um item ao pedido.',
            'items.*.item_name.required' => 'O nome do item é obrigatório.',
            'items.*.quantity.min' => 'A quantidade deve ser pelo menos 1.',
            'items.*.unit_price.min' => 'O preço unitário não pode ser negativo.',
        ];
    }
}
