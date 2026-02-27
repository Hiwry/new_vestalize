<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Marketplace\DesignerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class DesignerAuthController extends Controller
{
    /**
     * Exibir formulário de cadastro de designer
     */
    public function showRegister(): View
    {
        $specialties = DesignerProfile::$specialtyLabels;
        return view('marketplace.auth.designer-register', compact('specialties'));
    }

    /**
     * Processar cadastro do designer
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'display_name'  => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'specialties'   => ['required', 'array', 'min:1'],
            'specialties.*' => ['string', 'in:' . implode(',', array_keys(DesignerProfile::$specialtyLabels))],
            'bio'           => ['nullable', 'string', 'max:1000'],
            'instagram'     => ['nullable', 'string', 'max:255'],
            'behance'       => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'notify_new_orders' => ['nullable'],
        ]);

        // 1. Criar usuário com role designer (sem tenant)
        $user = User::create([
            'name'     => $request->display_name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->role = 'designer';
        $user->tenant_id = null;
        $user->save();

        // 2. Criar perfil de designer
        $slug = DesignerProfile::generateSlug($request->display_name);

        DesignerProfile::create([
            'user_id'           => $user->id,
            'slug'              => $slug,
            'display_name'      => $request->display_name,
            'bio'               => $request->bio,
            'specialties'       => $request->specialties,
            'instagram'         => $request->instagram,
            'behance'           => $request->behance,
            'portfolio_url'     => $request->portfolio_url,
            'status'            => 'pending',
            'commission_rate'   => 80,
            'notify_new_orders' => $request->has('notify_new_orders'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('marketplace.home')
            ->with('success', 'Bem-vindo! Seu perfil de designer foi criado e está em análise.');
    }

    /**
     * Exibir formulário de login de designer
     */
    public function showLogin(): View
    {
        return view('marketplace.auth.designer-login');
    }

    /**
     * Processar login do designer
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Verificar se é designer
            if (!$user->isDesigner()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Esta conta não é de designer. Use o login principal do Vestalize.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('marketplace.home'));
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    /**
     * Logout do designer
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('marketplace.home');
    }
}
