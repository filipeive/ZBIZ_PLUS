<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function tenantId(): int
    {
        return (int) ($this->user()?->tenant_id ?? current_tenant_id() ?? 0);
    }

    protected function prepareForValidation(): void
    {
        $this->request->remove('tenant_id');
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('tenant_id', $tenantId),
            ],
            'supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')->where('tenant_id', $tenantId),
            ],
            'linked_product_id' => [
                'nullable',
                Rule::exists('products', 'id')->where('tenant_id', $tenantId),
            ],
            'type'                       => ['required', 'in:product,physical,service'],
            'selling_price'              => ['required', 'numeric', 'min:0'],
            'purchase_price'             => ['nullable', 'numeric', 'min:0'],
            'promotional_price'          => ['nullable', 'numeric', 'min:0'],
            'is_on_promotion'            => ['nullable', 'boolean'],
            'promotion_discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'promotion_ends_at'          => ['nullable', 'date'],
            'barcode'                    => ['nullable', 'string', 'max:60'],
            'sku'                        => ['nullable', 'string', 'max:60'],
            'batch_number'               => ['nullable', 'string', 'max:60'],
            'expiry_date'                => ['nullable', 'date'],
            'manufacture_date'           => ['nullable', 'date'],
            'unit'                       => ['nullable', 'string', 'max:20'],
            'description'                => ['nullable', 'string', 'max:500'],
            'is_active'                  => ['nullable', 'boolean'],
        ];

        if (in_array($this->input('type'), ['product', 'physical'], true)) {
            $rules['stock_quantity'] = ['nullable', 'integer', 'min:0'];
            $rules['min_stock_level'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Informe o nome comercial ou descrição do artigo/serviço.',
            'name.max'                 => 'O nome do artigo não pode exceder 150 caracteres.',
            'category_id.required'     => 'Seleccione uma categoria para o artigo.',
            'category_id.exists'       => 'A categoria seleccionada não pertence a esta empresa.',
            'supplier_id.exists'       => 'O fornecedor seleccionado não pertence a esta empresa.',
            'linked_product_id.exists' => 'O insumo vinculado não pertence a esta empresa.',
            'type.required'            => 'Seleccione o tipo de item (Artigo Físico ou Serviço).',
            'type.in'                  => 'O tipo de item informado é inválido.',
            'selling_price.required'   => 'Informe o preço de venda em Meticais.',
            'selling_price.numeric'    => 'O preço de venda deve ser um valor numérico.',
            'selling_price.min'        => 'O preço de venda não pode ser negativo.',
            'purchase_price.min'       => 'O preço de compra não pode ser negativo.',
            'stock_quantity.min'       => 'A quantidade inicial em stock não pode ser negativa.',
            'min_stock_level.min'      => 'O nível mínimo de stock não pode ser negativo.',
        ];
    }
}
