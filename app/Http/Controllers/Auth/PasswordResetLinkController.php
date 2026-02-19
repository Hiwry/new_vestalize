<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Buscar usuário pelo email
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Não encontramos um usuário com este email.']);
        }

        // Gerar nova senha aleatória
        $newPassword = \Illuminate\Support\Str::password(10);

        // Atualizar senha do usuário
        $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $user->save();

        // Buscar código da loja (se o usuário tiver tenant)
        $storeCode = null;
        if ($user->tenant_id) {
            $tenant = \App\Models\Tenant::find($user->tenant_id);
            if ($tenant) {
                $storeCode = $tenant->store_code;
            }
        }

        // Enviar email com nova senha
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\PasswordResetMail($user, $newPassword, $storeCode)
            );

            return back()->with('status', 'Uma nova senha foi enviada para seu email! Verifique sua caixa de entrada e também a pasta de spam.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao enviar email de recuperação: ' . $e->getMessage());
            
            // Reverter a mudança de senha já que o email falhou
            $user->password = $user->getOriginal('password');
            $user->save();
            
            return back()->withErrors(['email' => 'Erro ao enviar o email. Por favor, tente novamente mais tarde ou entre em contato com o suporte.']);
        }
    }
}
