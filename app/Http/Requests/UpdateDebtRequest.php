<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtRequest extends StoreDebtRequest
{
    public function rules()
    {
        $rules = parent::rules();
        
        // Não validar paid_amount na edição pois ele é controlado pelos pagamentos
        unset($rules['paid_amount']);
        
        return $rules;
    }
}
