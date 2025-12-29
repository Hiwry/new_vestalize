<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Página de configurações organizadas por categoria
     */
    public function index(Request $request): View
    {
        $category = $request->get('category', 'admin'); // admin, estoque, caixa, vendedor, producao

        return view('settings.index', compact('category'));
    }
}
