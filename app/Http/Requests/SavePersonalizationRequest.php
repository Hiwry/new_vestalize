<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavePersonalizationRequest extends FormRequest
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
            'item_id' => 'required|exists:order_items,id',
            'personalization_type' => 'required|string',
            'personalization_id' => 'nullable|string',
            'art_name' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'size' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'color_count' => 'nullable|integer|min:0',
            'color_details' => 'nullable|string',
            'seller_notes' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
            'art_files.*' => 'nullable|file|max:10240',
            'editing_personalization_id' => 'nullable|integer|exists:order_sublimations,id',
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

        if ($this->has('final_price') && $this->final_price !== null) {
            $this->merge([
                'final_price' => str_replace(',', '.', $this->final_price),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'item_id.required' => 'O item é obrigatório.',
            'item_id.exists' => 'Item não encontrado.',
            'personalization_type.required' => 'O tipo de personalização é obrigatório.',
            'unit_price.required' => 'O preço unitário é obrigatório.',
            'unit_price.min' => 'O preço não pode ser negativo.',
            'final_price.required' => 'O preço final é obrigatório.',
            'final_price.min' => 'O preço não pode ser negativo.',
            'quantity.min' => 'A quantidade mínima é 1.',
            'art_files.*.max' => 'Cada arquivo pode ter no máximo 10MB.',
        ];
    }

    /**
     * Get data formatted for the service
     */
    public function getPersonalizationData(): array
    {
        return [
            'item_id' => $this->item_id,
            'personalization_type' => $this->personalization_type,
            'personalization_id' => $this->personalization_id,
            'art_name' => $this->art_name,
            'location' => $this->location,
            'size' => $this->size,
            'quantity' => $this->quantity ?? 1,
            'color_count' => $this->color_count ?? 0,
            'color_details' => $this->color_details,
            'seller_notes' => $this->seller_notes,
            'unit_price' => $this->unit_price,
            'final_price' => $this->final_price,
            'editing_personalization_id' => $this->editing_personalization_id,
        ];
    }
}
