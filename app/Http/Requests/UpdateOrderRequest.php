<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends StoreOrderRequest
{
    public function rules()
    {
        $rules = parent::rules();
        
        // Permitir data de entrega no passado para pedidos já criados
        $rules['delivery_date'] = 'nullable|date';
        
        // Adicionar validação para IDs dos itens existentes
        $rules['items.*.id'] = 'nullable|exists:order_items,id';
        
        return $rules;
    }
}
