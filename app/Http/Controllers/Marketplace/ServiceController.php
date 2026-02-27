<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\DesignerProfile;
use App\Models\Marketplace\MarketplaceService;
use App\Models\Marketplace\MarketplaceServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Listagem pública de serviços
     */
    public function index(Request $request)
    {
        $query = MarketplaceService::with(['designer.user', 'images'])
            ->active()
            ->whereHas('designer', fn($q) => $q->where('status', 'active'));

        if ($request->category) {
            $query->byCategory($request->category);
        }
        if ($request->max_credits) {
            $query->where('price_credits', '<=', $request->max_credits);
        }
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->sort === 'price_asc') {
            $query->orderBy('price_credits');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('price_credits');
        } elseif ($request->sort === 'rating') {
            $query->orderByDesc('rating_average');
        } else {
            $query->latest();
        }

        $services = $query->paginate(16)->withQueryString();
        $categories = MarketplaceService::$categoryLabels;

        return view('marketplace.services.index', compact('services', 'categories'));
    }

    /**
     * Detalhe público do serviço
     */
    public function show(int $id)
    {
        $service = MarketplaceService::with(['designer.user', 'images'])
            ->active()
            ->findOrFail($id);

        $relatedServices = MarketplaceService::with(['designer', 'images'])
            ->active()
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->take(4)
            ->get();

        $userWallet = null;
        if (Auth::check()) {
            $userWallet = \App\Models\Marketplace\MarketplaceCreditWallet::getOrCreate(Auth::id());
        }

        return view('marketplace.services.show', compact('service', 'relatedServices', 'userWallet'));
    }

    // ─── Designer: gestão dos próprios serviços ────────────────

    private function getDesignerOrFail(): DesignerProfile
    {
        $designer = DesignerProfile::where('user_id', Auth::id())->first();
        if (!$designer) {
            abort(403, 'Você precisa ser um designer para gerenciar serviços.');
        }
        return $designer;
    }

    public function create()
    {
        $designer = $this->getDesignerOrFail();
        $categories = MarketplaceService::$categoryLabels;
        return view('marketplace.services.create', compact('designer', 'categories'));
    }

    public function store(Request $request)
    {
        $designer = $this->getDesignerOrFail();

        $validated = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'required|string|max:2000',
            'category'      => 'required|in:' . implode(',', array_keys(MarketplaceService::$categoryLabels)),
            'price_credits' => 'required|integer|min:1|max:9999',
            'delivery_days' => 'required|integer|min:1|max:60',
            'requirements'  => 'nullable|string|max:500',
            'revisions'     => 'required|integer|min:0|max:10',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpg,png,webp|max:3072',
        ]);

        $service = $designer->services()->create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('marketplace/services', 'public');
                MarketplaceServiceImage::create([
                    'marketplace_service_id' => $service->id,
                    'path'       => $path,
                    'is_cover'   => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('marketplace.my-services.index')
            ->with('success', 'Serviço criado com sucesso!');
    }

    public function edit(int $id)
    {
        $designer = $this->getDesignerOrFail();
        $service  = $designer->services()->with('images')->findOrFail($id);
        $categories = MarketplaceService::$categoryLabels;
        return view('marketplace.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $designer = $this->getDesignerOrFail();
        $service  = $designer->services()->findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'required|string|max:2000',
            'category'      => 'required|in:' . implode(',', array_keys(MarketplaceService::$categoryLabels)),
            'price_credits' => 'required|integer|min:1|max:9999',
            'delivery_days' => 'required|integer|min:1|max:60',
            'requirements'  => 'nullable|string|max:500',
            'revisions'     => 'required|integer|min:0|max:10',
            'is_active'     => 'boolean',
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpg,png,webp|max:3072',
        ]);

        $service->update($validated);

        if ($request->hasFile('images')) {
            // Remove imagens anteriores
            foreach ($service->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('marketplace/services', 'public');
                MarketplaceServiceImage::create([
                    'marketplace_service_id' => $service->id,
                    'path'       => $path,
                    'is_cover'   => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('marketplace.my-services.index')
            ->with('success', 'Serviço atualizado!');
    }

    public function destroy(int $id)
    {
        $designer = $this->getDesignerOrFail();
        $service  = $designer->services()->findOrFail($id);

        foreach ($service->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $service->delete();

        return redirect()->route('marketplace.my-services.index')
            ->with('success', 'Serviço removido.');
    }
}
