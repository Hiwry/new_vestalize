<?php

namespace App\Http\Requests\Auth;

use App\Models\Tenant;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Tenant encontrado pelo código
     */
    protected ?Tenant $tenant = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_code' => ['nullable', 'string', 'size:6'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'store_code.required' => 'O código da loja é obrigatório.',
            'store_code.size' => 'O código da loja deve ter 6 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Informe um email válido.',
            'password.required' => 'A senha é obrigatória.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Tentar login como Super Admin (sem tenant) se não houver código de loja
        if (empty($this->store_code)) {
            $user = \App\Models\User::where('email', $this->email)->whereNull('tenant_id')->first();
            
            // Se o usuário existe como Super Admin (sem tenant)
            if ($user) {
                if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'tenant_id' => null], $this->boolean('remember'))) {
                    RateLimiter::clear($this->throttleKey());
                    return;
                }
                
                // Senha incorreta para o Super Admin
                throw ValidationException::withMessages([
                    'email' => 'Credenciais de administrador inválidas.',
                ]);
            }
            
            // Se o usuário não existe como Super Admin ou tem tenant, e não informou código
             throw ValidationException::withMessages([
                'store_code' => 'O código da loja é obrigatório para usuários de loja.',
            ]);
        }

        // 2. Fluxo Normal: Validar o código do tenant
        $this->tenant = Tenant::byCode($this->store_code)->first();

        if (!$this->tenant) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'store_code' => 'Código de loja inválido.',
            ]);
        }

        // Verificar se o tenant está ativo
        if ($this->tenant->status !== 'active') {
            throw ValidationException::withMessages([
                'store_code' => 'Esta conta está suspensa. Entre em contato com o suporte.',
            ]);
        }

        // Tentar autenticar o usuário dentro do tenant
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
            'tenant_id' => $this->tenant->id,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas para esta loja.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('store_code') . '|' . $this->string('email')) . '|' . $this->ip()
        );
    }
}

