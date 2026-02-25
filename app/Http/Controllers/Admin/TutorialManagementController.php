<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Models\TutorialCategory;
use Illuminate\Http\Request;

class TutorialManagementController extends Controller
{
    /**
     * List all categories with their tutorials.
     */
    public function index()
    {
        $categories = TutorialCategory::with('tutorials')
            ->orderBy('perfil')
            ->orderBy('ordem')
            ->get()
            ->groupBy('perfil');

        return view('admin.tutorials.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'perfil' => 'required|in:admin,admin_loja,vendedor,producao,caixa,estoque',
            'icone' => 'nullable|string|max:50',
            'cor' => 'nullable|string|max:20',
        ]);

        $slug = \Str::slug($request->nome);
        $baseSlug = $slug;
        $counter = 1;
        while (TutorialCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $maxOrder = TutorialCategory::where('perfil', $request->perfil)->max('ordem') ?? 0;

        TutorialCategory::create([
            'nome' => $request->nome,
            'slug' => $slug,
            'perfil' => $request->perfil,
            'icone' => $request->icone ?: 'fa-folder',
            'cor' => $request->cor ?: 'purple',
            'ordem' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, TutorialCategory $category)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'perfil' => 'required|in:admin,admin_loja,vendedor,producao,caixa,estoque',
            'icone' => 'nullable|string|max:50',
            'cor' => 'nullable|string|max:20',
        ]);

        $category->update([
            'nome' => $request->nome,
            'perfil' => $request->perfil,
            'icone' => $request->icone ?: $category->icone,
            'cor' => $request->cor ?: $category->cor,
        ]);

        return back()->with('success', 'Categoria atualizada!');
    }

    /**
     * Delete a category and all its tutorials.
     */
    public function destroyCategory(TutorialCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Categoria removida!');
    }

    /**
     * Toggle category active status.
     */
    public function toggleCategory(TutorialCategory $category)
    {
        $category->update(['ativo' => !$category->ativo]);
        return back()->with('success', $category->ativo ? 'Categoria ativada!' : 'Categoria desativada!');
    }

    /**
     * Store a new tutorial video.
     */
    public function storeTutorial(Request $request)
    {
        $request->validate([
            'tutorial_category_id' => 'required|exists:tutorial_categories,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'youtube_url' => 'required|string',
            'duracao' => 'nullable|string|max:10',
            'capa_url' => 'nullable|url|max:500',
        ]);

        $youtubeId = $this->extractYoutubeId($request->youtube_url);
        if (!$youtubeId) {
            return back()->withErrors(['youtube_url' => 'URL do YouTube inválida.'])->withInput();
        }

        $maxOrder = Tutorial::where('tutorial_category_id', $request->tutorial_category_id)->max('ordem') ?? 0;

        Tutorial::create([
            'tutorial_category_id' => $request->tutorial_category_id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'youtube_id' => $youtubeId,
            'duracao' => $request->duracao,
            'capa_url' => $request->capa_url,
            'ordem' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Tutorial adicionado com sucesso!');
    }

    /**
     * Update a tutorial video.
     */
    public function updateTutorial(Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'tutorial_category_id' => 'required|exists:tutorial_categories,id',
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string|max:500',
            'youtube_url' => 'required|string',
            'duracao' => 'nullable|string|max:10',
            'capa_url' => 'nullable|url|max:500',
        ]);

        $youtubeId = $this->extractYoutubeId($request->youtube_url);
        if (!$youtubeId) {
            return back()->withErrors(['youtube_url' => 'URL do YouTube inválida.'])->withInput();
        }

        $tutorial->update([
            'tutorial_category_id' => $request->tutorial_category_id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'youtube_id' => $youtubeId,
            'duracao' => $request->duracao,
            'capa_url' => $request->capa_url,
        ]);

        return back()->with('success', 'Tutorial atualizado!');
    }

    /**
     * Delete a tutorial video.
     */
    public function destroyTutorial(Tutorial $tutorial)
    {
        $tutorial->delete();
        return back()->with('success', 'Tutorial removido!');
    }

    /**
     * Toggle tutorial active status.
     */
    public function toggleTutorial(Tutorial $tutorial)
    {
        $tutorial->update(['ativo' => !$tutorial->ativo]);
        return back()->with('success', $tutorial->ativo ? 'Tutorial ativado!' : 'Tutorial desativado!');
    }

    /**
     * Extract YouTube video ID from various URL formats.
     */
    private function extractYoutubeId(string $url): ?string
    {
        // Already just an ID (11 chars alphanumeric)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', trim($url))) {
            return trim($url);
        }

        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
