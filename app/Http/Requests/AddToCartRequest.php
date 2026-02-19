<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'nullable|exists:products,id',
            'product_option_id' => 'nullable|exists:product_options,id',
            'item_type' => 'nullable|string|in:product,product_option,fabric_piece,machine,supply,uniform',
            'item_id' => 'nullable|integer',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'application_type' => 'nullable|in:sublimacao_local,dtf',
            'size_quantities' => 'nullable|array',
            'size_quantities.GG' => 'nullable|integer|min:0',
            'size_quantities.EXG' => 'nullable|integer|min:0',
            'size_quantities.G1' => 'nullable|integer|min:0',
            'size_quantities.G2' => 'nullable|integer|min:0',
            'size_quantities.G3' => 'nullable|integer|min:0',
            'sublocal_personalizations' => 'nullable|array',
            'sublocal_personalizations.*.location_id' => 'nullable|exists:sublimation_locations,id',
            'sublocal_personalizations.*.location_name' => 'nullable|string',
            'sublocal_personalizations.*.size_name' => 'nullable|string',
            'sublocal_personalizations.*.quantity' => 'nullable|integer|min:1',
            'sublocal_personalizations.*.unit_price' => 'nullable|numeric|min:0',
            'sublocal_personalizations.*.final_price' => 'nullable|numeric|min:0',
            // Campos para controle de estoque
            'size' => 'nullable|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'fabric_id' => 'nullable|exists:product_options,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter preços com vírgula para ponto
        if ($this->has('unit_price') && $this->unit_price !== null) {
            $this->merge([
                'unit_price' => str_replace(',', '.', $this->unit_price),
            ]);
        }

        if ($this->has('quantity') && $this->quantity !== null) {
            $this->merge([
                'quantity' => str_replace(',', '.', $this->quantity),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.min' => 'A quantidade mínima é 0,01.',
            'unit_price.min' => 'O preço não pode ser negativo.',
            'item_type.in' => 'Tipo de item inválido.',
            'size.in' => 'Tamanho inválido.',
        ];
    }

    /**
     * Determinar o tipo do item baseado nos dados
     */
    public function getItemType(): ?string
    {
        $type = $this->input('item_type');
        
        if (!$type) {
            if ($this->filled('product_id')) {
                return 'product';
            } elseif ($this->filled('product_option_id')) {
                return 'product_option';
            }
        }
        
        return $type;
    }
}
