<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    /**
     * Exibe a landing page ou redireciona para o dashboard se logado.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $plans = Plan::query()
            ->whereIn('slug', Plan::PUBLIC_SLUGS)
            ->orderBy('price')
            ->get();

        return view('landing.index', compact('plans'));
    }

    /**
     * Exibe a landing page para personalizados.
     * Não redireciona para dashboard mesmo se logado - landing page deve ser sempre acessível.
     */
    public function personalizados(): View
    {
        $plans = Plan::query()
            ->whereIn('slug', Plan::PUBLIC_SLUGS)
            ->orderBy('price')
            ->get();

        return view('landing-personalizados.index', compact('plans'));
    }
}
