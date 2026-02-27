<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\MarketplaceCreditWallet;
use App\Models\Marketplace\MarketplaceTool;
use App\Models\Marketplace\MarketplaceToolImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    /**
     * Listagem pública de ferramentas
     */
    public function index(Request $request)
    {
        $query = MarketplaceTool::with('images')->active();

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
        } else {
            $query->latest();
        }

        $tools      = $query->paginate(16)->withQueryString();
        $categories = MarketplaceTool::$categoryLabels;

        return view('marketplace.tools.index', compact('tools', 'categories'));
    }

    /**
     * Detalhe da ferramenta
     */
    public function show(int $id)
    {
        $tool = MarketplaceTool::with('images')->active()->findOrFail($id);

        $relatedTools = MarketplaceTool::with('images')
            ->active()
            ->where('category', $tool->category)
            ->where('id', '!=', $tool->id)
            ->take(4)
            ->get();

        $userWallet = null;
        $alreadyPurchased = false;
        if (Auth::check()) {
            $userWallet = MarketplaceCreditWallet::getOrCreate(Auth::id());
            $alreadyPurchased = \App\Models\Marketplace\MarketplaceOrder::where('buyer_id', Auth::id())
                ->where('orderable_type', 'tool')
                ->where('orderable_id', $tool->id)
                ->whereIn('status', ['completed', 'in_progress', 'delivered'])
                ->exists();
        }

        return view('marketplace.tools.show', compact('tool', 'relatedTools', 'userWallet', 'alreadyPurchased'));
    }

    /**
     * Download da ferramenta (após compra)
     */
    public function download(int $id)
    {
        $user = Auth::user();
        $tool = MarketplaceTool::findOrFail($id);

        // Verifica se o usuário comprou
        $order = \App\Models\Marketplace\MarketplaceOrder::where('buyer_id', $user->id)
            ->where('orderable_type', 'tool')
            ->where('orderable_id', $tool->id)
            ->whereIn('status', ['completed', 'in_progress', 'delivered'])
            ->first();

        if (!$order && !$user->isAdminGeral()) {
            abort(403, 'Você precisa comprar esta ferramenta para fazer download.');
        }

        if (!$tool->file_path || !Storage::disk('public')->exists($tool->file_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Incrementa contador de downloads
        $tool->increment('total_downloads');

        return Storage::disk('public')->download($tool->file_path, $tool->title . '.' . $tool->file_type);
    }

    // ─── Admin: CRUD de ferramentas ─────────────────────────────

    public function adminIndex()
    {
        $tools = MarketplaceTool::with('images')->latest()->paginate(20);
        $categories = MarketplaceTool::$categoryLabels;
        return view('admin.marketplace.tools.index', compact('tools', 'categories'));
    }

    public function adminCreate()
    {
        $categories = MarketplaceTool::$categoryLabels;
        return view('admin.marketplace.tools.create', compact('categories'));
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'required|string|max:2000',
            'category'      => 'required|in:' . implode(',', array_keys(MarketplaceTool::$categoryLabels)),
            'price_credits' => 'required|integer|min:1',
            'file'          => 'required|file|max:204800', // 200MB
            'images'        => 'nullable|array|max:6',
            'images.*'      => 'image|mimes:jpg,png,webp|max:3072',
            'is_featured'   => 'boolean',
        ]);

        $filePath = $request->file('file')->store('marketplace/tools/files', 'public');
        $fileType = $request->file('file')->getClientOriginalExtension();
        $fileSize = $request->file('file')->getSize();

        $tool = MarketplaceTool::create([
            ...$validated,
            'user_id'   => Auth::id(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('marketplace/tools/images', 'public');
                MarketplaceToolImage::create([
                    'marketplace_tool_id' => $tool->id,
                    'path'       => $path,
                    'is_cover'   => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.marketplace.tools.index')
            ->with('success', 'Ferramenta criada com sucesso!');
    }

    public function adminEdit(int $id)
    {
        $tool = MarketplaceTool::with('images')->findOrFail($id);
        $categories = MarketplaceTool::$categoryLabels;
        return view('admin.marketplace.tools.edit', compact('tool', 'categories'));
    }

    public function adminUpdate(Request $request, int $id)
    {
        $tool = MarketplaceTool::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'required|string|max:100',
            'description'   => 'required|string|max:2000',
            'category'      => 'required|in:' . implode(',', array_keys(MarketplaceTool::$categoryLabels)),
            'price_credits' => 'required|integer|min:1',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
            'file'          => 'nullable|file|max:204800',
            'images'        => 'nullable|array|max:6',
            'images.*'      => 'image|mimes:jpg,png,webp|max:3072',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($tool->file_path);
            $validated['file_path'] = $request->file('file')->store('marketplace/tools/files', 'public');
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();
            $validated['file_size'] = $request->file('file')->getSize();
        }

        $tool->update($validated);

        return redirect()->route('admin.marketplace.tools.index')
            ->with('success', 'Ferramenta atualizada!');
    }

    public function adminDestroy(int $id)
    {
        $tool = MarketplaceTool::findOrFail($id);
        if ($tool->file_path) Storage::disk('public')->delete($tool->file_path);
        foreach ($tool->images as $img) Storage::disk('public')->delete($img->path);
        $tool->delete();
        return redirect()->route('admin.marketplace.tools.index')
            ->with('success', 'Ferramenta removida.');
    }
}
