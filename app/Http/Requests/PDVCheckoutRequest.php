<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PDVCheckoutRequest extends FormRequest
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
            // Cliente
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            
            // Pagamento
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_methods' => 'nullable|array',
            'payment_methods.*.method' => 'required_with:payment_methods|string|in:dinheiro,pix,credito,debito,boleto,transferencia,cheque',
            'payment_methods.*.amount' => 'required_with:payment_methods|numeric|min:0',
            
            // Notas
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Converter valores monetários com vírgula para ponto
        $fieldsToConvert = ['discount', 'paid_amount'];
        
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
            'discount.min' => 'O desconto não pode ser negativo.',
            'paid_amount.min' => 'O valor pago não pode ser negativo.',
            'payment_methods.*.method.in' => 'Forma de pagamento inválida.',
            'payment_methods.*.amount.min' => 'O valor do pagamento não pode ser negativo.',
            'client_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
        ];
    }

    /**
     * Get data for cart operations
     */
    public function getCartData(): array
    {
        return [
            'client_id' => $this->client_id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
        ];
    }

    /**
     * Get data for payment operations
     */
    public function getPaymentData(): array
    {
        return [
            'discount' => $this->discount ?? 0,
            'paid_amount' => $this->paid_amount ?? 0,
            'payment_methods' => $this->payment_methods ?? [],
            'notes' => $this->notes,
        ];
    }
}
