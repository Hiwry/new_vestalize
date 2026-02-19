<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditOrderPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && session()->has('edit_order_id');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'entry_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_methods' => 'required|json',
            'size_surcharges' => 'nullable|json',
            'order_cover_image' => 'nullable|image|max:10240',
            'discount_type' => 'nullable|string|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter valores monetários com vírgula para ponto
        if ($this->has('delivery_fee') && $this->delivery_fee !== null) {
            $this->merge([
                'delivery_fee' => str_replace(',', '.', $this->delivery_fee),
            ]);
        }

        if ($this->has('discount_value') && $this->discount_value !== null) {
            $this->merge([
                'discount_value' => str_replace(',', '.', $this->discount_value),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'entry_date.required' => 'A data de entrada é obrigatória.',
            'entry_date.date' => 'Informe uma data válida.',
            'delivery_date.date' => 'Informe uma data de entrega válida.',
            'delivery_fee.min' => 'A taxa de entrega não pode ser negativa.',
            'payment_methods.required' => 'Informe ao menos uma forma de pagamento.',
            'discount_type.in' => 'Tipo de desconto inválido.',
            'discount_value.min' => 'O valor do desconto não pode ser negativo.',
            'order_cover_image.image' => 'O arquivo deve ser uma imagem.',
            'order_cover_image.max' => 'A imagem não pode ter mais de 10MB.',
        ];
    }

    /**
     * Get parsed payment methods
     */
    public function getPaymentMethods(): array
    {
        return json_decode($this->payment_methods, true) ?? [];
    }

    /**
     * Get parsed size surcharges
     */
    public function getSizeSurcharges(): array
    {
        return json_decode($this->size_surcharges ?? '{}', true) ?? [];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $subtotalWithFees): float
    {
        $discountType = $this->discount_type ?? 'none';
        $discountValue = floatval($this->discount_value ?? 0);

        if ($discountType === 'percentage') {
            $discountValue = max(0, min(100, $discountValue));
            return ($subtotalWithFees * $discountValue) / 100.0;
        } elseif ($discountType === 'fixed') {
            return min($discountValue, $subtotalWithFees);
        }

        return 0;
    }
}
