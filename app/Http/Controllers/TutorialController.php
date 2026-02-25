<?php

namespace App\Http\Controllers;

use App\Models\TutorialCategory;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index(Request $request)
    {
        $categoria = $request->query('categoria', 'admin');
        
        // Get all active categories grouped by profile
        $allCategories = TutorialCategory::ativo()
            ->withCount(['activeTutorials'])
            ->orderBy('ordem')
            ->get()
            ->groupBy('perfil');

        // Get categories for the selected profile with their active tutorials
        $categorias = TutorialCategory::ativo()
            ->perfil($categoria)
            ->with('activeTutorials')
            ->orderBy('ordem')
            ->get();

        return view('tutorials.index', [
            'categoria' => $categoria,
            'categorias' => $categorias,
            'allCategories' => $allCategories,
        ]);
    }
}
