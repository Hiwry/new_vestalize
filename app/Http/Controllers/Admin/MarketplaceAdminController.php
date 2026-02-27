<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\DesignerProfile;
use App\Models\Marketplace\MarketplaceCreditPackage;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\MarketplaceService;
use App\Models\Marketplace\MarketplaceTool;
use Illuminate\Http\Request;

class MarketplaceAdminController extends Controller
{
    /**
     * Painel admin do marketplace
     */
    public function index()
    {
        $stats = [
            'total_designers'        => DesignerProfile::count(),
            'active_designers'       => DesignerProfile::where('status', 'active')->count(),
            'pending_designers'      => DesignerProfile::where('status', 'pending')->count(),
            'total_services'         => MarketplaceService::count(),
            'total_tools'            => MarketplaceTool::count(),
            'total_orders'           => MarketplaceOrder::count(),
            'orders_in_progress'     => MarketplaceOrder::where('status', 'in_progress')->count(),
            'orders_completed'       => MarketplaceOrder::where('status', 'completed')->count(),
            'total_credits_transacted' => MarketplaceOrder::where('status', 'completed')->sum('price_credits'),
        ];

        $pendingDesigners = DesignerProfile::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $recentOrders = MarketplaceOrder::with(['buyer', 'designer.user'])
            ->latest()
            ->take(10)
            ->get();

        $packages = MarketplaceCreditPackage::orderBy('sort_order')->get();

        return view('admin.marketplace.index', compact('stats', 'pendingDesigners', 'recentOrders', 'packages'));
    }

    /**
     * Aprova ou suspende um designer
     */
    public function updateDesignerStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,suspended,pending',
        ]);

        $designer = DesignerProfile::findOrFail($id);
        $designer->update(['status' => $validated['status']]);

        $label = match($validated['status']) {
            'active' => 'aprovado',
            'suspended' => 'suspenso',
            default => 'alterado',
        };

        return back()->with('success', "Designer {$designer->display_name} foi {$label}.");
    }

    /**
     * Listagem de serviços com moderação
     */
    public function services(Request $request)
    {
        $services = MarketplaceService::with(['designer.user', 'images'])
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(20);

        return view('admin.marketplace.services', compact('services'));
    }

    /**
     * Toggle destaque / ativo de um serviço
     */
    public function toggleService(Request $request, int $id)
    {
        $service = MarketplaceService::findOrFail($id);
        $field   = $request->field === 'featured' ? 'is_featured' : 'is_active';
        $service->toggle($field);
        return back();
    }

    // ─── Pacotes de Créditos CRUD ──────────────────────────────

    public function packagesIndex()
    {
        $packages = MarketplaceCreditPackage::orderBy('sort_order')->get();
        return view('admin.marketplace.packages.index', compact('packages'));
    }

    public function packagesCreate()
    {
        return view('admin.marketplace.packages.create');
    }

    public function packagesStore(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'credits'          => 'required|integer|min:1',
            'price'            => 'required|numeric|min:0.01',
            'subscriber_price' => 'required|numeric|min:0.01',
            'badge'            => 'nullable|string|max:50',
            'is_featured'      => 'boolean',
            'sort_order'       => 'integer',
        ]);

        MarketplaceCreditPackage::create($validated);

        return redirect()->route('admin.marketplace.packages.index')
            ->with('success', 'Pacote criado!');
    }

    public function packagesEdit(int $id)
    {
        $package = MarketplaceCreditPackage::findOrFail($id);
        return view('admin.marketplace.packages.edit', compact('package'));
    }

    public function packagesUpdate(Request $request, int $id)
    {
        $package = MarketplaceCreditPackage::findOrFail($id);

        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'credits'          => 'required|integer|min:1',
            'price'            => 'required|numeric|min:0.01',
            'subscriber_price' => 'required|numeric|min:0.01',
            'badge'            => 'nullable|string|max:50',
            'is_featured'      => 'boolean',
            'is_active'        => 'boolean',
            'sort_order'       => 'integer',
        ]);

        $package->update($validated);

        return redirect()->route('admin.marketplace.packages.index')
            ->with('success', 'Pacote atualizado!');
    }

    public function packagesDestroy(int $id)
    {
        MarketplaceCreditPackage::findOrFail($id)->delete();
        return redirect()->route('admin.marketplace.packages.index')
            ->with('success', 'Pacote removido.');
    }
}
