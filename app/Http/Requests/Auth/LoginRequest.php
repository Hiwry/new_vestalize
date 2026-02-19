<?php

namespace App\Http\Requests\Auth;

use App\Models\Tenant;
use App\Models\User;
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

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_code' => ['nullable', 'string', 'size:6'],
            'email'      => ['required', 'string', 'email'],
            'password'   => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_code.required' => 'O código da loja é obrigatório.',
            'store_code.size'     => 'O código da loja deve ter 6 caracteres.',
            'email.required'      => 'O email é obrigatório.',
            'email.email'         => 'Informe um email válido.',
            'password.required'   => 'A senha é obrigatória.',
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

        // 1. Tentar login como Admin Geral (sem tenant) se não houver código de loja
        if (empty($this->store_code)) {
            $user = User::where('email', $this->email)
                ->where(function ($query) {
                    $query->whereNull('tenant_id')
                          ->orWhere('role', 'admin_geral');
                })
                ->first();

            if ($user) {
                $credentials = ['email' => $this->email, 'password' => $this->password];
                if (is_null($user->tenant_id)) {
                    $credentials['tenant_id'] = null;
                }

                if (Auth::attempt($credentials, $this->boolean('remember'))) {
                    RateLimiter::clear($this->throttleKey());
                    return;
                }

                throw ValidationException::withMessages([
                    'email' => 'Credenciais de administrador inválidas.',
                ]);
            }

            throw ValidationException::withMessages([
                'store_code' => 'O código da loja é obrigatório para usuários de loja.',
            ]);
        }

        // 2. Fluxo normal com código de loja
        $this->tenant = Tenant::byCode($this->store_code)->first();

        if (!$this->tenant) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'store_code' => 'Código de loja inválido.',
            ]);
        }

        if ($this->tenant->status !== 'active') {
            throw ValidationException::withMessages([
                'store_code' => 'Esta conta está suspensa. Entre em contato com o suporte.',
            ]);
        }

        $credentials = [
            'email'     => $this->email,
            'password'  => $this->password,
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

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('store_code') . '|' . $this->string('email')) . '|' . $this->ip()
        );
    }
}
