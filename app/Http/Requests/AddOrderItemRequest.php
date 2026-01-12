<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOrderItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && session()->has('current_order_id');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'personalizacao' => 'required|array|min:1',
            'personalizacao.*' => 'exists:product_options,id',
            'tecido' => 'required|exists:product_options,id',
            'tipo_tecido' => 'nullable|exists:product_options,id',
            'cor' => 'required|exists:product_options,id',
            'tipo_corte' => 'required|exists:product_options,id',
            'detalhe' => 'nullable|exists:product_options,id',
            'gola' => 'required|exists:product_options,id',
            'tamanhos' => 'required|array',
            'tamanhos.*' => 'integer|min:0',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
            'apply_surcharge' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter preços com vírgula para ponto
        if ($this->has('unit_price')) {
            $this->merge([
                'unit_price' => str_replace(',', '.', $this->unit_price),
            ]);
        }
        
        if ($this->has('unit_cost')) {
            $this->merge([
                'unit_cost' => str_replace(',', '.', $this->unit_cost),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'personalizacao.required' => 'Selecione pelo menos uma personalização.',
            'tecido.required' => 'O tecido é obrigatório.',
            'cor.required' => 'A cor é obrigatória.',
            'tipo_corte.required' => 'O tipo de corte é obrigatório.',
            'gola.required' => 'O tipo de gola é obrigatório.',
            'tamanhos.required' => 'Informe ao menos um tamanho.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.min' => 'A quantidade mínima é 1.',
            'unit_price.required' => 'O preço unitário é obrigatório.',
            'unit_price.min' => 'O preço deve ser maior ou igual a zero.',
            'item_cover_image.image' => 'O arquivo deve ser uma imagem.',
            'item_cover_image.max' => 'A imagem não pode ter mais de 10MB.',
        ];
    }
}
