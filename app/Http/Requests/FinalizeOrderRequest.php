<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizeOrderRequest extends FormRequest
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
            'delivery_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:2000',
            'discount' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'is_event' => 'nullable|boolean',
            
            // Pagamento
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_methods' => 'nullable|array',
            'payment_methods.*.method' => 'required_with:payment_methods|string',
            'payment_methods.*.amount' => 'required_with:payment_methods|numeric|min:0',
            'payment_notes' => 'nullable|string|max:500',
            
            // Termos
            'terms_accepted' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter valores monetários com vírgula para ponto
        $fieldsToConvert = ['discount', 'delivery_fee', 'paid_amount'];
        
        foreach ($fieldsToConvert as $field) {
            if ($this->has($field) && $this->$field !== null) {
                $this->merge([
                    $field => str_replace(',', '.', $this->$field),
                ]);
            }
        }
        
        // Converter valores em payment_methods
        if ($this->has('payment_methods') && is_array($this->payment_methods)) {
            $methods = collect($this->payment_methods)->map(function ($method) {
                if (isset($method['amount'])) {
                    $method['amount'] = str_replace(',', '.', $method['amount']);
                }
                return $method;
            })->toArray();
            
            $this->merge(['payment_methods' => $methods]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'delivery_date.required' => 'A data de entrega é obrigatória.',
            'delivery_date.date' => 'Informe uma data válida.',
            'delivery_date.after_or_equal' => 'A data de entrega não pode ser anterior a hoje.',
            'discount.min' => 'O desconto não pode ser negativo.',
            'delivery_fee.min' => 'A taxa de entrega não pode ser negativa.',
            'paid_amount.min' => 'O valor pago não pode ser negativo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'delivery_date' => 'data de entrega',
            'notes' => 'observações',
            'discount' => 'desconto',
            'delivery_fee' => 'taxa de entrega',
            'paid_amount' => 'valor pago',
            'payment_methods' => 'formas de pagamento',
            'payment_notes' => 'observações do pagamento',
        ];
    }
}
