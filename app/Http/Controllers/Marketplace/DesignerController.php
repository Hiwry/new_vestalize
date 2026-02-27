<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\DesignerProfile;
use App\Models\Marketplace\MarketplaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DesignerController extends Controller
{
    /**
     * Exibe perfil público do designer
     */
    public function show(string $slug)
    {
        $designer = DesignerProfile::with(['user', 'services.images'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $services = $designer->services()
            ->active()
            ->with('images')
            ->get();

        return view('marketplace.designers.show', compact('designer', 'services'));
    }

    /**
     * Formulário de cadastro como designer
     */
    public function setup()
    {
        $user = Auth::user();

        // Se já tem perfil, redirecionar para edição
        $existing = DesignerProfile::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->route('marketplace.designer.edit')
                ->with('info', 'Você já possui um perfil de designer.');
        }

        $specialties = DesignerProfile::$specialtyLabels;

        return view('marketplace.designers.setup', compact('specialties'));
    }

    /**
     * Salva o cadastro como designer
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Verificar se já existe perfil
        if (DesignerProfile::where('user_id', $user->id)->exists()) {
            return redirect()->route('marketplace.designer.edit');
        }

        $validated = $request->validate([
            'display_name'  => 'required|string|max:100',
            'bio'           => 'required|string|max:1000',
            'specialties'   => 'required|array|min:1',
            'specialties.*' => 'in:' . implode(',', array_keys(DesignerProfile::$specialtyLabels)),
            'avatar'        => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'instagram'     => 'nullable|string|max:100',
            'behance'       => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')
                ->store('marketplace/avatars', 'public');
        }

        $slug = DesignerProfile::generateSlug($validated['display_name']);

        DesignerProfile::create([
            'user_id'       => $user->id,
            'slug'          => $slug,
            'display_name'  => $validated['display_name'],
            'bio'           => $validated['bio'],
            'specialties'   => $validated['specialties'],
            'avatar'        => $avatarPath,
            'instagram'     => $validated['instagram'] ?? null,
            'behance'       => $validated['behance'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'status'        => 'active', // Auto-aprovado (pode mudar para 'pending' se quiser aprovação manual)
        ]);

        return redirect()->route('marketplace.my-services.index')
            ->with('success', 'Perfil de designer criado! Agora adicione seus serviços.');
    }

    /**
     * Formulário de edição do perfil
     */
    public function edit()
    {
        $designer = DesignerProfile::where('user_id', Auth::id())->firstOrFail();
        $specialties = DesignerProfile::$specialtyLabels;

        return view('marketplace.designers.edit', compact('designer', 'specialties'));
    }

    /**
     * Atualiza perfil do designer
     */
    public function update(Request $request)
    {
        $designer = DesignerProfile::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'display_name'  => 'required|string|max:100',
            'bio'           => 'required|string|max:1000',
            'specialties'   => 'required|array|min:1',
            'specialties.*' => 'in:' . implode(',', array_keys(DesignerProfile::$specialtyLabels)),
            'avatar'        => 'nullable|image|mimes:jpg,png,webp|max:2048',
            'instagram'     => 'nullable|string|max:100',
            'behance'       => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            // Remove avatar antigo
            if ($designer->avatar) {
                Storage::disk('public')->delete($designer->avatar);
            }
            $validated['avatar'] = $request->file('avatar')
                ->store('marketplace/avatars', 'public');
        }

        $designer->update($validated);

        return redirect()->route('marketplace.designer.edit')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Meus serviços (painel do designer)
     */
    public function myServices()
    {
        $designer = DesignerProfile::where('user_id', Auth::id())->first();

        if (!$designer) {
            return redirect()->route('marketplace.designer.setup');
        }

        $services = $designer->services()->with('images')->latest()->get();

        return view('marketplace.designers.my-services', compact('designer', 'services'));
    }
}
