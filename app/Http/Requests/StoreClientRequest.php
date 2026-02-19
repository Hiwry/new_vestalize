<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:12',
            'category' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cliente é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'phone_primary.required' => 'O telefone principal é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'state.max' => 'O estado deve ter no máximo 2 caracteres (sigla).',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'phone_primary' => 'telefone principal',
            'phone_secondary' => 'telefone secundário',
            'email' => 'e-mail',
            'cpf_cnpj' => 'CPF/CNPJ',
            'address' => 'endereço',
            'city' => 'cidade',
            'state' => 'estado',
            'zip_code' => 'CEP',
            'category' => 'categoria',
        ];
    }
}
